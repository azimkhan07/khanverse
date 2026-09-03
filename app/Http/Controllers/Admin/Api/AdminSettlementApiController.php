<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminSettlement;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminSettlementApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AdminSettlement::with(['order', 'seller', 'buyer'])
            ->where('status', $request->get('status', 'pending'));

        if ($request->search) {
            $s = "%{$request->search}%";
            $query->where(function ($q) use ($s) {
                $q->where('order_number', 'like', $s)
                    ->orWhereHas('seller', fn ($b) => $b->where('full_name', 'like', $s))
                    ->orWhereHas('buyer', fn ($b) => $b->where('full_name', 'like', $s));
            });
        }

        $settlements = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($settlements);
    }

    public function show($id): JsonResponse
    {
        $settlement = AdminSettlement::with(['order', 'seller', 'buyer', 'project'])
            ->findOrFail($id);

        return response()->json($settlement);
    }

    public function markPaid(Request $request, $id): JsonResponse
    {
        $settlement = AdminSettlement::where('status', 'pending')->findOrFail($id);

        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        $txnId = 'STL-' . strtoupper(Str::random(10));

        DB::beginTransaction();
        try {
            // Credit seller wallet
            $seller = $settlement->seller;
            $sellerUser = $seller->user ?? null;
            if ($sellerUser) {
                $sw = Wallet::firstOrCreate(
                    ['user_id' => $sellerUser->id],
                    ['balance' => 0, 'pending_balance' => 0, 'withdrawn_balance' => 0]
                );
                $newBalance = (float) $sw->balance + (float) $settlement->seller_amount;
                $sw->update(['balance' => $newBalance]);
                WalletTransaction::create([
                    'wallet_id' => $sw->id,
                    'user_id' => $sellerUser->id,
                    'type' => 'earning',
                    'credit' => $settlement->seller_amount,
                    'debit' => 0,
                    'balance_after' => $newBalance,
                    'reference_id' => $settlement->id,
                    'reference_type' => 'settlement',
                    'remarks' => 'Settlement for order ' . $settlement->order_number,
                ]);
            }

            // Credit admin wallet (platform fee)
            $adminUser = User::where('email', 'admin@khanverse.com')->first();
            if ($adminUser) {
                $aw = Wallet::firstOrCreate(
                    ['user_id' => $adminUser->id],
                    ['balance' => 0, 'pending_balance' => 0, 'withdrawn_balance' => 0]
                );
                $adminNewBalance = (float) $aw->balance + (float) $settlement->platform_fee;
                $aw->update(['balance' => $adminNewBalance]);
                WalletTransaction::create([
                    'wallet_id' => $aw->id,
                    'user_id' => $adminUser->id,
                    'type' => 'commission',
                    'credit' => $settlement->platform_fee,
                    'debit' => 0,
                    'balance_after' => $adminNewBalance,
                    'reference_id' => $settlement->id,
                    'reference_type' => 'settlement',
                    'remarks' => 'Platform fee from order ' . $settlement->order_number,
                ]);
            }

            $settlement->update([
                'status' => 'paid',
                'transaction_id' => $txnId,
                'paid_at' => now(),
                'admin_note' => $request->admin_note ?? null,
            ]);

            // Notify seller
            NotificationService::send(
                $sellerUser?->id,
                'Payment Settled',
                '₹' . number_format($settlement->seller_amount, 2) . ' for order ' . $settlement->order_number . ' has been credited to your wallet.',
                'payment',
                '/seller/wallet',
                ['settlement_id' => $settlement->id, 'amount' => $settlement->seller_amount],
            );

            DB::commit();

            return response()->json([
                'message' => 'Settlement paid successfully. Transaction: ' . $txnId,
                'settlement' => $settlement->fresh(['order', 'seller', 'buyer']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Payment failed: ' . $e->getMessage()], 500);
        }
    }

    public function stats(): JsonResponse
    {
        $pending = AdminSettlement::where('status', 'pending')->sum('seller_amount');
        $paid = AdminSettlement::where('status', 'paid')->sum('seller_amount');
        $pendingCount = AdminSettlement::where('status', 'pending')->count();
        $paidCount = AdminSettlement::where('status', 'paid')->count();
        $totalFee = AdminSettlement::where('status', 'paid')->sum('platform_fee');

        return response()->json([
            'pending_amount' => $pending,
            'paid_amount' => $paid,
            'pending_count' => $pendingCount,
            'paid_count' => $paidCount,
            'total_platform_fee' => $totalFee,
        ]);
    }
}