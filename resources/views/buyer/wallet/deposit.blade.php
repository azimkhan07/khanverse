@extends('buyer.layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="mb-1">Add Funds</h3>

                <small class="text-muted">
                    Deposit money into your wallet.
                </small>

            </div>

            <a href="{{ route('buyer.wallet.index') }}" class="btn btn-secondary">

                <i class="ti-arrow-left"></i>

                Back

            </a>

        </div>

        <div class="row justify-content-center">

            <div class="col-lg-6">

                <div class="card shadow-sm border-0">

                    <div class="card-header">

                        <strong>Add Balance</strong>

                    </div>

                    <div class="card-body">

                        <form action="{{ route('buyer.wallet.deposit.store') }}" method="POST">

                            @csrf

                            <div class="mb-3">

                                <label class="form-label">

                                    Amount

                                </label>

                                <input type="number" name="amount" class="form-control" min="100" step="0.01"
                                    placeholder="Enter Amount" required>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">

                                    Payment Method

                                </label>

                                <select name="payment_method" class="form-control">

                                    <option value="razorpay">

                                        Razorpay

                                    </option>

                                    <option value="stripe">

                                        Stripe

                                    </option>

                                </select>

                            </div>

                            <div class="alert alert-info mb-4">

                                After clicking <strong>Proceed</strong>,
                                payment gateway will open.

                            </div>

                            <button class="btn btn-primary">

                                <i class="ti-wallet"></i>

                                Proceed to Payment

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
