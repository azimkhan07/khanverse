<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Public invoice lookup by scannable token (invoice number).
     */
    private function findInvoice(string $token): ?Invoice
    {
        return Invoice::where('invoice_number', $token)->first();
    }

    /**
     * Stream the invoice PDF inline (opens in browser / mobile scanner).
     */
    public function view(Request $request, string $token)
    {
        $invoice = $this->findInvoice($token);

        if (!$invoice) {
            abort(404);
        }

        $pdf = InvoiceService::buildPdf($invoice);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $invoice->invoice_number . '.pdf"',
        ]);
    }

    /**
     * Force download of the invoice PDF.
     */
    public function download(Request $request, string $token)
    {
        $invoice = $this->findInvoice($token);

        if (!$invoice) {
            abort(404);
        }

        $pdf = InvoiceService::buildPdf($invoice);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $invoice->invoice_number . '.pdf"',
        ]);
    }
}
