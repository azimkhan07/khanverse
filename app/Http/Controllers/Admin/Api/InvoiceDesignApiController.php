<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Invoice;
use App\Models\InvoiceDesign;
use App\Models\Order;
use App\Models\Seller;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceDesignApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = InvoiceDesign::query();

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $designs = $query->latest()->paginate($request->get('per_page', 10));

        return response()->json($designs);
    }

    public function show($id): JsonResponse
    {
        return response()->json(InvoiceDesign::findOrFail($id));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatePayload($request);

        $design = InvoiceDesign::create($validated);

        return response()->json([
            'message' => 'Invoice design created successfully.',
            'design' => $design,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $design = InvoiceDesign::findOrFail($id);

        $validated = $this->validatePayload($request, $design);

        $design->update($validated);

        // Notify every user that has buyer or seller access.
        $this->notifyDesignUpdate();

        return response()->json([
            'message' => 'Invoice design updated successfully.',
            'design' => $design->fresh(),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        InvoiceDesign::findOrFail($id)->delete();

        return response()->json(['message' => 'Invoice design deleted successfully.']);
    }

    public function toggleStatus($id): JsonResponse
    {
        $design = InvoiceDesign::findOrFail($id);
        $design->update(['is_active' => !$design->is_active]);

        return response()->json([
            'message' => 'Invoice design status updated.',
            'design' => $design->fresh(),
        ]);
    }

    /**
     * Preview a design using live data (latest completed order) so the admin
     * can see the design + content before committing it to invoices.
     */
    public function preview($id): JsonResponse
    {
        $design = InvoiceDesign::findOrFail($id);

        $order = Order::where('status', 'completed')->latest()->first()
            ?? Order::latest()->first();

        if (!$order) {
            return response()->json([
                'message' => 'No order available to preview the invoice with.',
                'html' => null,
            ], 200);
        }

        // Temporarily render the invoice using this specific design.
        $html = $this->renderWithDesign($design, $order);

        return response()->json([
            'message' => 'Preview generated.',
            'html' => $html,
            'order_number' => $order->order_number,
        ]);
    }

    private function validatePayload(Request $request, ?InvoiceDesign $existing = null): array
    {
        $data = $request->validate([
            'type' => 'required|in:buyer,seller',
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'logo_text' => 'nullable|string|max:255',
            'logo_url' => 'nullable|string|max:500',
            'footer_line' => 'nullable|string',
            'about_line' => 'nullable|string',
            'body' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        return array_merge($data, [
            'is_active' => array_key_exists('is_active', $data)
                ? (bool) $data['is_active']
                : ($existing ? $existing->is_active : true),
        ]);
    }

    /**
     * Render a completed order's invoice as the given design, without persisting.
     */
    private function renderWithDesign(InvoiceDesign $design, Order $order): string
    {
        $amount = (float) $order->amount;
        $feePct = InvoiceService::feePercent();
        $fee = round($amount * $feePct / 100, 2);
        $total = $design->type === 'seller' ? round($amount - $fee, 2) : round($amount + $fee, 2);

        $buyer = $order->buyer;
        $seller = $order->seller;
        $buyerUser = $buyer?->user;
        $sellerUser = $seller?->user;

        $data = [
            'invoice_number' => 'PREVIEW-' . $order->id,
            'serial_number' => 'PREVIEW-' . $order->id,
            'invoice_type' => ucfirst($design->type),
            'order_number' => $order->order_number ?? ('#' . $order->id),
            'order_id' => (string) $order->id,
            'service_title' => $order->service?->title ?? 'Service',
            'project_title' => $order->project?->title ?? $order->service?->title ?? 'Project',
            'issue_date' => now()->format('d M Y'),
            'due_date' => optional($order->delivery_date)->format('d M Y'),
            'buyer_name' => $buyerUser?->name ?: ($buyer?->full_name ?? 'Buyer'),
            'buyer_email' => $buyerUser?->email ?? '',
            'buyer_company' => $buyer?->company_name ?? '',
            'buyer_country' => $buyer?->country ?? '',
            'buyer_city' => $buyer?->city ?? '',
            'seller_name' => $sellerUser?->name ?: ($seller?->full_name ?? 'Seller'),
            'seller_email' => $sellerUser?->email ?? '',
            'seller_country' => $seller?->country ?? '',
            'seller_city' => $seller?->city ?? '',
            'amount' => number_format($amount, 2),
            'fee' => number_format($fee, 2),
            'fee_pct' => $feePct,
            'total' => number_format($total, 2),
            'currency' => 'â‚¹',
        ];

        $html = $this->renderShell($design, $data, $design->body ?: '');
        $html = str_replace('{BODY}', $design->body ?: '', $html);
        $html = str_replace('{BARCODE}', \App\Services\BarcodeService::html(route('invoice.view', 'PREVIEW-' . $order->id), 40), $html);

        $search = [];
        $replace = [];
        foreach ($data as $k => $v) {
            $search[] = '{{' . $k . '}}';
            $replace[] = e((string) $v);
        }

        return str_replace($search, $replace, $html);
    }

    private function renderShell(InvoiceDesign $design, array $d, string $body): string
    {
        $company = $design->company_name ?: 'SkillNest';
        $logoText = $design->logo_text ?: $company;
        $footerLine = $design->footer_line ?: ('Powered by ' . $company);
        $aboutLine = $design->about_line ?: '';

        $logo = $design->logo_url
            ? '<img src="' . e($design->logo_url) . '" style="max-height:48px;"/>'
            : '<div style="font-size:22px;font-weight:800;color:#1e293b;">' . e($logoText) . '</div>';

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: 'DejaVu Sans', Arial, sans-serif; color:#0f172a; font-size:13px; }
    .inv-header { border-bottom:3px solid #2563eb; padding-bottom:16px; margin-bottom:18px; overflow:hidden; }
    .inv-logo { float:left; }
    .inv-barcode { float:right; text-align:center; padding-top:4px; }
    .inv-barcode .serial { font-size:9px; color:#64748b; margin-top:4px; letter-spacing:1px; }
    .party { width:48%; vertical-align:top; }
    .party h4 { margin:0 0 6px; font-size:11px; text-transform:uppercase; color:#64748b; }
    table.det { width:100%; border-collapse:collapse; margin:16px 0; }
    table.det th, table.det td { border:1px solid #cbd5e1; padding:8px 10px; text-align:left; }
    table.det th { background:#2563eb; color:#fff; }
    .tot { width:100%; margin-top:6px; }
    .tot td { padding:5px 8px; }
    .tot .lab { text-align:right; width:70%; color:#475569; }
    .tot .val { text-align:right; width:30%; font-weight:600; }
</style>
</head>
<body>
    <div class="inv-header">
        <div class="inv-logo">{$logo}</div>
        <div class="inv-barcode">
            {BARCODE}
            <div class="serial">{$d['serial_number']}</div>
        </div>
        <div style="clear:both;"></div>
    </div>
    <p><strong>{$d['invoice_type']} Invoice</strong> &mdash; {$d['invoice_number']} (Order {$d['order_number']})</p>
    <p><strong>From:</strong> {$d['seller_name']}, {$d['seller_email']}</p>
    <p><strong>Bill To:</strong> {$d['buyer_name']}, {$d['buyer_email']}</p>
    {BODY}
    <table class="tot">
        <tr><td class="lab">Subtotal</td><td class="val">{$d['currency']}{$d['amount']}</td></tr>
        <tr><td class="lab">Platform Fee ({$d['fee_pct']}%)</td><td class="val">{$d['currency']}{$d['fee']}</td></tr>
        <tr><td class="lab">Total ({$d['invoice_type']})</td><td class="val">{$d['currency']}{$d['total']}</td></tr>
    </table>
    <p style="font-size:11px;color:#64748b;">{$footerLine}</p>
</body>
</html>
HTML;
    }

    /**
     * Notify all users that have seller or buyer access about the design update.
     */
    private function notifyDesignUpdate(): void
    {
        $userIds = User::where(function ($q) {
            $q->whereHas('seller')
                ->orWhereHas('buyer');
            })->pluck('id');

        foreach ($userIds as $userId) {
            NotificationService::send(
                $userId,
                'Invoice Design Updated',
                'We updated the design of our marketplace invoices. Your future invoices will use the new design automatically.',
                'invoice',
                null,
                ['type' => 'design_update'],
            );
        }
    }
}

