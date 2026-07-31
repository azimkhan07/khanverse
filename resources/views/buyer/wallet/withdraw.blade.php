@extends('seller.layouts.app')

@section('title', 'Withdraw Request')

@section('content')

    <div class="page-body">

        <div class="page-header">

            <div class="page-header-title">

                <h4>

                    <i class="feather icon-download"></i>

                    Withdraw Request

                </h4>

            </div>

        </div>

        <div class="row">
            <div class="col-lg-4">

                <div class="card">

                    <div class="card-header">

                        <h5>

                            Wallet Summary

                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="mb-3">

                            <label class="text-muted">

                                Available Balance

                            </label>

                            <h3 class="text-success">

                                ₹ {{ number_format($wallet->balance, 2) }}

                            </h3>

                        </div>

                        <div class="mb-3">

                            <label class="text-muted">

                                Pending Balance

                            </label>

                            <h5 class="text-warning">

                                ₹ {{ number_format($wallet->pending_balance, 2) }}

                            </h5>

                        </div>

                        <div>

                            <label class="text-muted">

                                Withdrawn

                            </label>

                            <h5 class="text-primary">

                                ₹ {{ number_format($wallet->withdrawn_balance, 2) }}

                            </h5>

                        </div>

                    </div>

                </div>

            </div>
            <div class="col-lg-8">

                <div class="card">

                    <div class="card-header">

                        <h5>

                            Request Withdrawal

                        </h5>

                    </div>

                    <div class="card-body">

                        <form method="POST" action="{{ route('seller.wallet.withdraw.request') }}">

                            @csrf
                            <div class="mb-3">

                                <label>

                                    Withdraw Amount

                                </label>

                                <input type="number" name="amount" step="0.01" min="1" class="form-control"
                                    placeholder="Enter Amount" required>

                            </div>

                            <div class="mb-3">

                                <label>

                                    Payment Method

                                </label>

                                <select name="payment_method" class="form-control" required>

                                    <option value="">

                                        Select

                                    </option>

                                    <option value="bank">

                                        Bank Transfer

                                    </option>

                                    <option value="upi">

                                        UPI

                                    </option>

                                    <option value="paypal">

                                        PayPal

                                    </option>

                                </select>

                            </div>

                            <div class="mb-3">

                                <label>

                                    Account Details

                                </label>

                                <textarea name="account_details" rows="5" class="form-control" placeholder="Enter Bank / UPI / PayPal Details"
                                    required></textarea>

                            </div>

                            <div class="alert alert-info">

                                <h6>

                                    Important

                                </h6>

                                <ul class="mb-0">

                                    <li>

                                        Minimum withdraw amount is ₹100.

                                    </li>

                                    <li>

                                        Withdraw request will be reviewed by Admin.

                                    </li>

                                    <li>

                                        Approved amount will be transferred to your selected payment method.

                                    </li>

                                </ul>

                            </div>

                            <div class="text-end">

                                <a href="{{ route('seller.wallet.index') }}" class="btn btn-secondary">

                                    Cancel

                                </a>

                                <button type="submit" class="btn btn-success">

                                    Submit Request

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>

    </div>

@endsection
