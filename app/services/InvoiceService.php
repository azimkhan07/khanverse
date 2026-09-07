<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceDesign;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    /**
     * Commission cut percentage applied by the marketplace.
     */
    public static function feePercent(): float
    {
        return (float) setting('invoice', 'platform_fee_pct', 12);
    }

    /**
     * Public view token (used by the scannable barcode QR).
     */
    public static function publicToken(Invoice $invoice): string
    {
        return $invoice->invoice_number;
    }

    /**
     * Generate buyer + seller invoices for a completed order.
     * Returns the created invoices (buyer and seller).
     */
    public static function generateForOrder(Order $order): array
    {
        $amount = (float) $order->amount;
        $feePct = self::feePercent();
        $fee = round($amount * $feePct / 100, 2);

        $buyerInvoice = self::createOne($order, 'buyer', $amount, $fee);
        $sellerInvoice = self::createOne($order, 'seller', $amount, $fee);

        return ['buyer' => $buyerInvoice, 'seller' => $sellerInvoice];
    }

    private static function createOne(Order $order, string $type, float $amount, float $fee): Invoice
    {
        $invoiceNumber = NumberService::next('invoices', self::invoicePattern(), 'invoice_counter');

        // Buyer invoice: pays amount + fee. Seller invoice: gross - fee.
        if ($type === 'seller') {
            $total = round($amount - $fee, 2);
        } else {
            $total = round($amount + $fee, 2);
        }

        // The scannable value is the public URL that opens/downloads the PDF.
        $barcodeValue = self::invoiceViewUrl($invoiceNumber);

        return Invoice::updateOrCreate(
            ['order_id' => $order->id, 'type' => $type],
            [
                'order_id' => $order->id,
                'project_id' => $order->project_id,
                'buyer_id' => $order->buyer_id,
                'seller_id' => $order->seller_id,
                'invoice_number' => $invoiceNumber,
                'type' => $type,
                'amount' => $amount,
                'platform_fee' => $type === 'seller' ? $fee : $fee,
                'total' => $total,
                'barcode_value' => $barcodeValue,
                'status' => 'generated',
                'generated_at' => now(),
            ]
        );
    }

    private static function invoicePattern(): string
    {
        return setting('invoices', 'pattern', '{prefix}{year}{seq}');
    }

    public static function orderPattern(): string
    {
        return setting('orders', 'pattern', 'ORD-{year}-{seq}');
    }

    /**
     * Pull an order number for a new order. Uses the current order pattern + counter.
     */
    public static function nextOrderNumber(): string
    {
        return NumberService::next('orders', self::orderPattern(), 'order_counter');
    }

    private static function invoiceViewUrl(string $invoiceNumber): string
    {
        try {
            return route('invoice.view', $invoiceNumber);
        } catch (\Throwable $e) {
            return url('/invoice/' . $invoiceNumber);
        }
    }

    /**
     * Build the full invoice HTML (branding + latest design of the matching type).
     */
    public static function renderHtml(Invoice $invoice): string
    {
        $order = $invoice->order()->with(['buyer.user', 'seller.user', 'service', 'project'])->first()
            ?? $invoice->order()->with(['buyer.user', 'seller.user', 'service'])->first();

        $design = InvoiceDesign::where('type', $invoice->type)
            ->where('is_active', true)
            ->latest()
            ->first();

        $data = self::invoiceData($invoice, $order);

        $body = $design && $design->body ? $design->body : self::defaultBody();

        $html = self::pageShell($design, $data, $body);
        $html = str_replace('{BODY}', $body, $html);
        $html = str_replace('{BARCODE}', BarcodeService::html((string) $invoice->barcode_value, 40), $html);

        return self::replacePlaceholders($html, $data);
    }

    /**
     * Render and return the PDF byte string.
     */
    public static function buildPdf(Invoice $invoice): string
    {
        $html = self::renderHtml($invoice);

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('a4');

        return $pdf->output();
    }

    /**
     * Every value available to the design template.
     */
    private static function invoiceData(Invoice $invoice, ?Order $order): array
    {
        $buyer = $invoice->buyer;
        $seller = $invoice->seller;
        $buyerUser = $buyer?->user;
        $sellerUser = $seller?->user;

        $orderNumber = $order?->order_number ?? ('#' . $invoice->order_id);
        $serviceTitle = $order?->service?->title ?? 'Service';
        $projectTitle = $order?->project?->title ?? $order?->service?->title ?? 'Project';

        return [
            'invoice_number' => $invoice->invoice_number,
            'serial_number' => $invoice->invoice_number,
            'invoice_type' => ucfirst($invoice->type),
            'order_number' => $orderNumber,
            'order_id' => (string) $invoice->order_id,
            'service_title' => $serviceTitle,
            'project_title' => $projectTitle,
            'issue_date' => optional($invoice->generated_at)->format('d M Y'),
            'due_date' => optional($order?->delivery_date)->format('d M Y'),

            'buyer_name' => $buyerUser?->name ?: ($buyer?->full_name ?? 'Buyer'),
            'buyer_email' => $buyerUser?->email ?? '',
            'buyer_company' => $buyer?->company_name ?? '',
            'buyer_country' => $buyer?->country ?? '',
            'buyer_city' => $buyer?->city ?? '',

            'seller_name' => $sellerUser?->name ?: ($seller?->full_name ?? 'Seller'),
            'seller_email' => $sellerUser?->email ?? '',
            'seller_bio' => $seller?->bio ?? '',
            'seller_country' => $seller?->country ?? '',
            'seller_city' => $seller?->city ?? '',

            'amount' => number_format((float) $invoice->amount, 2),
            'fee' => number_format((float) $invoice->platform_fee, 2),
            'fee_pct' => self::feePercent(),
            'total' => number_format((float) $invoice->total, 2),
            'currency' => '₹',
        ];
    }

    private static function pageShell(?InvoiceDesign $design, array $d, string $body): string
    {
        $company = $design?->company_name ?: setting('invoice', 'company_name', 'SkillNest');
        $logoText = $design?->logo_text ?: setting('invoice', 'logo_text', $company);
        $logoUrl = $design?->logo_url ?: setting('invoice', 'logo_url', '');
        $footerLine = $design?->footer_line ?: setting('invoice', 'footer_line', 'Powered by ' . $company);
        $aboutLine = $design?->about_line ?: setting('invoice', 'about_line', '');

        $logo = '';
        if ($logoUrl) {
            $logo = '<img src="' . e($logoUrl) . '" style="max-height:48px;"/>';
        } else {
            $logo = '<div style="font-size:22px;font-weight:800;color:#1e293b;">' . e($logoText) . '</div>';
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; color:#0f172a; margin:0; padding:0; font-size:13px; }
    .inv-wrap { max-width:760px; margin:0 auto; padding:22px; }
    .inv-header { border-bottom:3px solid #2563eb; padding-bottom:16px; margin-bottom:18px; overflow:hidden; }
    .inv-logo { float:left; }
    .inv-barcode { float:right; text-align:center; padding-top:4px; }
    .inv-barcode .serial { font-size:9px; color:#64748b; margin-top:4px; letter-spacing:1px; }
    .inv-title { font-size:26px; font-weight:800; color:#2563eb; letter-spacing:1px; }
    .party { width:48%; vertical-align:top; padding:6px; }
    .party h4 { margin:0 0 6px; font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#64748b; }
    .party .nm { font-weight:700; font-size:14px; margin-bottom:4px; }
    .party div { line-height:1.5; }
    table.det { width:100%; border-collapse:collapse; margin:16px 0; }
    table.det th, table.det td { border:1px solid #cbd5e1; padding:8px 10px; text-align:left; }
    table.det th { background:#2563eb; color:#fff; font-weight:600; }
    .tot { width:100%; margin-top:6px; }
    .tot td { padding:5px 8px; }
    .tot .lab { text-align:right; width:70%; color:#475569; }
    .tot .val { text-align:right; width:30%; font-weight:600; }
    .tot .grand-b .lab { font-size:16px; font-weight:800; color:#0f172a; }
    .tot .grand-b .val { font-size:18px; font-weight:800; color:#2563eb; }
    .inv-footer { margin-top:28px; border-top:1px dashed #cbd5e1; padding-top:10px; font-size:11px; color:#64748b; text-align:center; }
    .inv-footer .about { margin-top:4px; }
    .lp { height:1px; }
</style>
</head>
<body>
<div class="inv-wrap">
    <div class="inv-header">
        <div class="inv-logo">{$logo}<div style="font-size:12px;color:#475569;">{$d['invoice_type']} Invoice</div></div>
        <div class="inv-barcode">
            {BARCODE}
            <div class="serial">{$d['serial_number']}</div>
        </div>
        <div style="clear:both;"></div>
    </div>

    <div style="overflow:hidden;">
        <div class="party" style="float:left;">
            <h4>From / Seller</h4>
            <div class="nm">{$d['seller_name']}</div>
            <div>{$d['seller_email']}</div>
            <div>{$d['seller_city']}, {$d['seller_country']}</div>
        </div>
        <div class="party" style="float:right;">
            <h4>Bill To / Buyer</h4>
            <div class="nm">{$d['buyer_name']}</div>
            <div>{$d['buyer_email']}</div>
            <div>{$d['buyer_company']} {$d['buyer_city']}, {$d['buyer_country']}</div>
        </div>
        <div style="clear:both;"></div>
    </div>

    <table class="det">
        <tr>
            <th>Description</th>
            <th style="width:120px;">Project / Service</th>
            <th style="width:100px;">Amount</th>
        </tr>
        <tr>
            <td>{$d['order_number']} &mdash; {$d['service_title']}</td>
            <td>{$d['project_title']}</td>
            <td>{$d['currency']}{$d['amount']}</td>
        </tr>
    </table>

    {BODY}

    <table class="tot">
        <tr><td class="lab">Subtotal</td><td class="val">{$d['currency']}{$d['amount']}</td></tr>
        <tr><td class="lab">Platform Fee ({$d['fee_pct']}%)</td><td class="val">{$d['currency']}{$d['fee']}</td></tr>
        <tr class="grand-b"><td class="lab">Total ({$d['invoice_type']})</td><td class="val">{$d['currency']}{$d['total']}</td></tr>
    </table>

    <div class="inv-footer">
        <div>{$footerLine}</div>
        <div class="about">{$aboutLine}</div>
    </div>
</div>
</body>
</html>
HTML;
    }

    private static function defaultBody(): string
    {
        return '<table class="det"><tr><th colspan="3">Invoice Summary</th></tr></table>';
    }

    /**
     * Substitute placeholders like {{key}} with values. Also inject the QR image src.
     */
    private static function replacePlaceholders(string $html, array $data): string
    {
        $search = [];
        $replace = [];

        // QR src should not be html-escaped (it's a data URI with base64).
        unset($data['qr_src']);

        foreach ($data as $key => $value) {
            $search[] = '{{' . $key . '}}';
            $replace[] = e((string) $value);
        }

        return str_replace($search, $replace, $html);
    }
}
