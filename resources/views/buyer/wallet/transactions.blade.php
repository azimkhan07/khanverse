@extends('buyer.layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="mb-1">
                    Wallet Transactions
                </h3>

                <small class="text-muted">
                    View all wallet activities.
                </small>

            </div>

            <a href="{{ route('buyer.wallet.index') }}" class="btn btn-secondary">

                <i class="ti-arrow-left"></i>

                Back

            </a>

        </div>

        <div class="card shadow-sm border-0">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Type</th>

                                <th>Description</th>

                                <th>Amount</th>

                                <th>Status</th>

                                <th>Date</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($transactions as $transaction)
                                <tr>

                                    <td>

                                        {{ $transaction->id }}

                                    </td>

                                    <td>

                                        <span class="badge bg-{{ $transaction->type == 'credit' ? 'success' : 'danger' }}">

                                            {{ ucfirst($transaction->type) }}

                                        </span>

                                    </td>

                                    <td>

                                        {{ $transaction->description }}

                                    </td>

                                    <td>

                                        <strong
                                            class="{{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">

                                            {{ $transaction->type == 'credit' ? '+' : '-' }}

                                            ₹{{ number_format($transaction->amount, 2) }}

                                        </strong>

                                    </td>

                                    <td>

                                        <span class="badge bg-info">

                                            {{ ucfirst($transaction->status) }}

                                        </span>

                                    </td>

                                    <td>

                                        {{ $transaction->created_at->format('d M Y') }}

                                        <br>

                                        <small class="text-muted">

                                            {{ $transaction->created_at->format('h:i A') }}

                                        </small>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="text-center py-5">

                                        <h5>

                                            No Transactions Found

                                        </h5>

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <div class="mt-4">

            {{ $transactions->links() }}

        </div>

    </div>
@endsection
