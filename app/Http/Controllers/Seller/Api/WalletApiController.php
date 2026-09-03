<?php

namespace App\Http\Controllers\Seller\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletApiController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'pending_balance' => 0, 'withdrawn_balance' => 0]
        );

        return response()->json($wallet);
    }

    public function transactions(Request $request): JsonResponse
    {
        $user = Auth::user();

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($transactions);
    }

    public function showTransaction($id): JsonResponse
    {
        $user = Auth::user();

        $transaction = WalletTransaction::where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json($transaction);
    }

    public function withdrawRequest(Request $request): JsonResponse
    {
        $user = Auth::user();

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'account_details' => 'required|string',
        ]);

        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet || $wallet->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient balance.'], 422);
        }

        $withdraw = WithdrawRequest::create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'amount' => $request->amount,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'account_details' => $request->account_details,
        ]);

        return response()->json([
            'message' => 'Withdrawal request submitted.',
            'withdraw' => $withdraw,
        ], 201);
    }

    public function withdrawHistory(Request $request): JsonResponse
    {
        $user = Auth::user();

        $history = WithdrawRequest::where('user_id', $user->id)
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($history);
    }
}
