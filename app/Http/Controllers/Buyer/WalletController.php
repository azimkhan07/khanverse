<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function index()
    {
        $buyer = Auth::user()->buyer;
        $wallet = Wallet::firstOrCreate(
            ['user_id' => Auth::id()],
            ['balance' => 0]
        );

        $transactions = WalletTransaction::where('user_id', $buyer->id)->latest()->paginate(15);

        return view('buyer.wallet.index', compact('wallet', 'transactions'));
    }

    public function transactions(Request $request)
    {
        $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();
        $transactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })->latest()->paginate(15);

        return view('buyer.wallet.transactions', compact('wallet', 'transactions'));
    }

    public function deposit()
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => Auth::id()],
            ['balance' => 0]
        );

        return view('buyer.wallet.deposit', compact('wallet'));
    }

    public function depositStore(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100|max:100000',
            'payment_method' => 'required|in:razorpay,stripe,bank',
        ]);

        $buyer = Auth::user()->buyer;

        WalletTransaction::create([
            'wallet_id'      => $buyer->wallet->id,
            'type'           => 'deposit',
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method,
            'status'         => 'pending',
            'reference'      => 'DEP-' . strtoupper(Str::random(10)),
        ]);

        return redirect()->route('buyer.wallet.transactions')->with('success', 'Deposit request created successfully.');
    }
}
