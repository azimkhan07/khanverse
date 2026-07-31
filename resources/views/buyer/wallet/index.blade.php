@extends('buyer.layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="mb-1">

                    My Wallet

                </h3>

                <small class="text-muted">

                    Manage your wallet balance and transactions.

                </small>

            </div>

            <div>

                <a href="{{ route('buyer.wallet.deposit') }}" class="btn btn-primary">

                    <i class="ti-plus"></i>

                    Add Balance

                </a>

            </div>

        </div>

        <div class="row">

            <div class="col-lg-4 mb-4">

                <div class="card shadow-sm border-0">

                    <div class="card-body text-center">

                        <h6 class="text-muted">

                            Available Balance

                        </h6>

                        <h2 class="text-success mb-0">

                            ₹{{ number_format($wallet->balance ?? 0, 2) }}

                        </h2>

                    </div>

                </div>

            </div>

            <div class="col-lg-4 mb-4">

                <div class="card shadow-sm border-0">

                    <div class="card-body text-center">

                        <h6 class="text-muted">

                            Total Deposits

                        </h6>

                        <h2 class="text-primary mb-0">

                            ₹{{ number_format($totalDeposit ?? 0, 2) }}

                        </h2>

                    </div>

                </div>

            </div>

            <div class="col-lg-4 mb-4">

                <div class="card shadow-sm border-0">

                    <div class="card-body text-center">

                        <h6 class="text-muted">

                            Total Spent

                        </h6>

                        <h2 class="text-danger mb-0">

                            ₹{{ number_format($totalSpent ?? 0, 2) }}

                        </h2>

                    </div>

                </div>

            </div>

        </div>

        <div class="card shadow-sm border-0">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>

                    Recent Transactions

                </strong>

                <a href="{{ route('buyer.wallet.transactions') }}" class="btn btn-sm btn-primary">

                    View All

                </a>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Type</th>

                                <th>Amount</th>

                                <th>Status</th>

                                <th>Date</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($transactions as $transaction)
                                <tr>

                                    <td>

                                        #{{ $transaction->id }}

                                    </td>

                                    <td>

                                        {{ ucfirst($transaction->type) }}

                                    </td>

                                    <td>

                                        @if ($transaction->type == 'deposit')
                                            <span class="text-success">

                                                + ₹{{ number_format($transaction->amount, 2) }}

                                            </span>
                                        @else
                                            <span class="text-danger">

                                                - ₹{{ number_format($transaction->amount, 2) }}

                                            </span>
                                        @endif

                                    </td>

                                    <td>

                                        @php

                                            $color = [
                                                'pending' => 'warning',
                                                'success' => 'success',
                                                'failed' => 'danger',
                                            ];

                                        @endphp

                                        <span class="badge bg-{{ $color[$transaction->status] ?? 'secondary' }}">

                                            {{ ucfirst($transaction->status) }}

                                        </span>

                                    </td>

                                    <td>

                                        {{ $transaction->created_at->format('d M Y') }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5" class="text-center py-5">

                                        No Transactions Found

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
@endsection
