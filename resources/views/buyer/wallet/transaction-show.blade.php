@extends('seller.layouts.app')

@section('title', 'Transaction Details')

@section('content')

    <div class="page-body">

        <div class="page-header">

            <div class="page-header-title">

                <h4>

                    <i class="feather icon-file-text"></i>

                    Transaction Details

                </h4>

            </div>

        </div>

        <div class="card">

            <div class="card-header">

                <div class="d-flex justify-content-between align-items-center">

                    <h5>

                        Transaction Information

                    </h5>

                    <a href="{{ route('seller.wallet.transactions') }}" class="btn btn-primary">

                        <i class="feather icon-arrow-left"></i>

                        Back

                    </a>

                </div>

            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="fw-bold">Transaction ID</label>

                        <p>#{{ $transaction->id }}</p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="fw-bold">Transaction Type</label>

                        <p>{{ ucfirst($transaction->type) }}</p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="fw-bold">Credit</label>

                        <p class="text-success">

                            ₹ {{ number_format($transaction->credit, 2) }}

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="fw-bold">Debit</label>

                        <p class="text-danger">

                            ₹ {{ number_format($transaction->debit, 2) }}

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="fw-bold">Balance After Transaction</label>

                        <p>

                            ₹ {{ number_format($transaction->balance_after, 2) }}

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="fw-bold">Reference Type</label>

                        <p>

                            {{ $transaction->reference_type ?? '-' }}

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="fw-bold">Reference ID</label>

                        <p>

                            {{ $transaction->reference_id ?? '-' }}

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="fw-bold">Transaction Date</label>

                        <p>

                            {{ $transaction->created_at->format('d M Y h:i A') }}

                        </p>

                    </div>

                </div>
                <div class="mb-3">

                    <label class="fw-bold">

                        Remarks

                    </label>

                    <div class="border rounded p-3 bg-light">

                        {{ $transaction->remarks ?? 'No Remarks Available' }}

                    </div>

                </div>
                <div class="text-end">

                    <a href="{{ route('seller.wallet.transactions') }}" class="btn btn-secondary">

                        Back

                    </a>

                </div>

            </div>

        </div>

    </div>

@endsection
