<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Seller;
use App\Models\WalletTransaction;
use App\Models\WithdrawRequest;

class WalletController extends Controller
{
    public function index()
    {
        $seller = Seller::where('user_id', Auth::id())->first();
        $wallet = Wallet::firstOrCreate(['user_id' => Auth::id()], ['balance' => 0, 'pending_balance' => 0, 'withdrawn_balance' => 0,]);

        $transactions = WalletTransaction::where('user_id', Auth::id())->latest()->take(10)->get();

        return view('seller.wallet.index', compact('seller', 'wallet', 'transactions'));
    }

    public function transactions()
    {
        $seller = Seller::where('user_id', Auth::id())->first();

        $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();

        $transactions = $wallet->transactions()->latest()->get();

        return view(
            'seller.wallet.transactions',
            compact('seller', 'wallet', 'transactions')
        );
    }

    public function withdrawForm()
    {
        $seller = Seller::where('user_id', Auth::id())->first();

        $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();

        return view(
            'seller.wallet.withdraw',
            compact('seller', 'wallet')

        );
    }

    public function withdrawRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();

        if ($request->amount > $wallet->balance) {
            return back()->with('error', 'Insufficient wallet balance.');
        }

        return back()->with('success', 'Withdraw request submitted successfully.');
    }

    public function withdrawHistory()
    {
        $seller = Seller::where('user_id', Auth::id())->first();
        $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();
        $withdraws = WithdrawRequest::where('user_id', Auth::id())->latest()->get();

        return view('seller.wallet.withdraw-history', compact('seller', 'wallet', 'withdraws'));
    }

    public function showTransaction($id)
    {
        $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();
        $transaction = WalletTransaction::where( 'wallet_id', $wallet->id)->findOrFail($id);

        return view('seller.wallet.transaction-show', compact('wallet','transaction'));
    }
}
