<?php

namespace App\Http\Controllers\Buyer\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
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

    public function deposit(Request $request): JsonResponse
    {
        $user = Auth::user();

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $wallet = Wallet::where('user_id', $user->id)->first();
        $wallet->increment('balance', $request->amount);

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'type' => 'deposit',
            'credit' => $request->amount,
            'balance_after' => $wallet->fresh()->balance,
            'remarks' => 'Wallet deposit',
        ]);

        return response()->json([
            'message' => 'Deposit successful.',
            'wallet' => $wallet->fresh(),
        ]);
    }
}
