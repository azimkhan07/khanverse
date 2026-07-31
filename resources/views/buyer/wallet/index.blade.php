@extends('seller.layouts.app')

@section('title', 'My Wallet')

@section('content')

    <div class="page-body">
        <div class="page-header">
            <div class="page-header-title">
                <h4>
                    <i class="feather icon-credit-card"></i>
                    My Wallet
                </h4>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-header">
                    <h5> Quick Actions </h5>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('seller.wallet.withdraw.form') }}" class="btn btn-success">
                        <i class="feather icon-download"></i>
                        Withdraw Money
                    </a>
                    <a href="{{ route('seller.wallet.transactions') }}" class="btn btn-primary">
                        <i class="feather icon-list"></i>
                        View All Transactions
                    </a>
                    <a href="{{ route('seller.wallet.withdraw.history') }}" class="btn btn-warning">
                        <i class="feather icon-clock"></i>
                        Withdraw History
                    </a>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5> Recent Transactions </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Credit</th>
                                    <th>Debit</th>
                                    <th>Balance</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if ($transactions->count())

                                    @foreach ($transactions as $transaction)
                                        <tr>

                                            <td>

                                                {{ $transaction->created_at->format('d M Y') }}

                                                <br>

                                                <small class="text-muted">

                                                    {{ $transaction->created_at->format('h:i A') }}

                                                </small>

                                            </td>

                                            <td>

                                                <span class="badge bg-info">

                                                    {{ ucfirst($transaction->type) }}

                                                </span>

                                            </td>

                                            <td class="text-success fw-bold">

                                                @if ($transaction->credit > 0)
                                                    + ₹ {{ number_format($transaction->credit, 2) }}
                                                @else
                                                    -
                                                @endif

                                            </td>

                                            <td class="text-danger fw-bold">

                                                @if ($transaction->debit > 0)
                                                    - ₹ {{ number_format($transaction->debit, 2) }}
                                                @else
                                                    -
                                                @endif

                                            </td>

                                            <td>

                                                ₹ {{ number_format($transaction->balance_after, 2) }}

                                            </td>

                                            <td>

                                                {{ $transaction->remarks ?? '-' }}

                                            </td>

                                        </tr>
                                    @endforeach
                                @else
                                    <tr>

                                        <td colspan="6" class="text-center text-muted">

                                            No Transactions Found

                                        </td>

                                    </tr>

                                @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
