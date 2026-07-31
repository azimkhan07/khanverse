@extends('seller.layouts.app')

@section('title', 'Wallet Transactions')

@section('content')

    <div class="page-body">

        <div class="page-header">

            <div class="page-header-title">

                <h4>

                    <i class="feather icon-list"></i>

                    Wallet Transactions

                </h4>

            </div>

        </div>

        <div class="card">

            <div class="card-header">

                <div class="d-flex justify-content-between align-items-center">

                    <h5>

                        Transaction History

                    </h5>

                    <a href="{{ route('seller.wallet.index') }}" class="btn btn-primary">

                        <i class="feather icon-arrow-left"></i>

                        Back

                    </a>

                </div>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover" id="walletTable">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Date</th>

                                <th>Type</th>

                                <th>Credit</th>

                                <th>Debit</th>

                                <th>Balance</th>

                                <th>Remarks</th>

                                <th>Action</th>

                            </tr>

                        </thead>

                        <tbody>
                            @if ($transactions->count())

                                @foreach ($transactions as $transaction)
                                    <tr>

                                        <td>

                                            {{ $loop->iteration }}

                                        </td>

                                        <td>

                                            {{ $transaction->created_at->format('d M Y') }}

                                            <br>

                                            <small class="text-muted">

                                                {{ $transaction->created_at->format('h:i A') }}

                                            </small>

                                        </td>

                                        <td>

                                            @switch($transaction->type)
                                                @case('order')
                                                    <span class="badge bg-success">

                                                        Order

                                                    </span>
                                                @break

                                                @case('withdraw')
                                                    <span class="badge bg-warning">

                                                        Withdraw

                                                    </span>
                                                @break

                                                @case('refund')
                                                    <span class="badge bg-danger">

                                                        Refund

                                                    </span>
                                                @break

                                                @default
                                                    <span class="badge bg-info">

                                                        {{ ucfirst($transaction->type) }}

                                                    </span>
                                            @endswitch

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

                                        <td>

                                            <a href="{{ route('seller.wallet.transaction.show', $transaction->id) }}"
                                                class="btn btn-sm btn-primary">

                                                <i class="feather icon-eye"></i>

                                            </a>

                                        </td>

                                    </tr>
                                @endforeach
                            @else
                                <tr>

                                    <td colspan="8" class="text-center">

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

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#walletTable').DataTable({
                pageLength: 25,
                order: [[1, 'desc']],
                responsive: true
            });
        });
    </script>
@endpush
