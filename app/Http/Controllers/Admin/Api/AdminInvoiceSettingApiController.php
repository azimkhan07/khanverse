<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminInvoiceSettingApiController extends Controller
{
    /**
     * Get order numbering settings.
     */
    public function orderSettings(): JsonResponse
    {
        return response()->json([
            'pattern' => setting('orders', 'pattern', 'ORD-{year}-{seq}'),
            'prefix' => setting('orders', 'prefix', 'ORD'),
            'suffix' => setting('orders', 'suffix', ''),
            'seq_padding' => (int) setting('orders', 'seq_padding', 4),
        ]);
    }

    /**
     * Update order numbering settings.
     */
    public function updateOrderSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pattern' => 'required|string|max:255',
            'prefix' => 'nullable|string|max:50',
            'suffix' => 'nullable|string|max:50',
            'seq_padding' => 'nullable|integer|min:1|max:12',
        ]);

        $this->setMany('orders', $validated);

        return response()->json([
            'message' => 'Order numbering settings updated.',
        ]);
    }

    /**
     * Get invoice numbering + base branding/fee settings.
     */
    public function invoiceSettings(): JsonResponse
    {
        return response()->json([
            'pattern' => setting('invoices', 'pattern', '{prefix}{year}{seq}'),
            'prefix' => setting('invoices', 'prefix', 'INV'),
            'suffix' => setting('invoices', 'suffix', ''),
            'seq_padding' => (int) setting('invoices', 'seq_padding', 4),
            'platform_fee_pct' => (float) setting('invoice', 'platform_fee_pct', 12),
            'company_name' => setting('invoice', 'company_name', 'KhanVerse'),
            'logo_text' => setting('invoice', 'logo_text', 'KhanVerse'),
            'logo_url' => setting('invoice', 'logo_url', ''),
            'footer_line' => setting('invoice', 'footer_line', 'Powered by KhanVerse'),
            'about_line' => setting('invoice', 'about_line', ''),
        ]);
    }

    /**
     * Update invoice numbering + base branding settings.
     */
    public function updateInvoiceSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pattern' => 'required|string|max:255',
            'prefix' => 'nullable|string|max:50',
            'suffix' => 'nullable|string|max:50',
            'seq_padding' => 'nullable|integer|min:1|max:12',
            'platform_fee_pct' => 'nullable|numeric|min:0|max:100',
            'company_name' => 'nullable|string|max:255',
            'logo_text' => 'nullable|string|max:255',
            'logo_url' => 'nullable|string|max:500',
            'footer_line' => 'nullable|string',
            'about_line' => 'nullable|string',
        ]);

        $this->setMany('invoices', [
            'pattern' => $validated['pattern'],
            'prefix' => $validated['prefix'] ?? '',
            'suffix' => $validated['suffix'] ?? '',
            'seq_padding' => $validated['seq_padding'] ?? 4,
        ]);

        $this->setMany('invoice', [
            'platform_fee_pct' => $validated['platform_fee_pct'] ?? 12,
            'company_name' => $validated['company_name'] ?? '',
            'logo_text' => $validated['logo_text'] ?? '',
            'logo_url' => $validated['logo_url'] ?? '',
            'footer_line' => $validated['footer_line'] ?? '',
            'about_line' => $validated['about_line'] ?? '',
        ]);

        return response()->json([
            'message' => 'Invoice settings updated.',
        ]);
    }

    private function setMany(string $group, array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(
                ['group' => $group, 'key' => $key],
                ['value' => (string) $value, 'type' => 'text']
            );
        }
    }
}
