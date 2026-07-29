@extends('seller.layouts.app')

@section('title', 'Withdraw History')

@section('content')

    <div class="page-body">

        <div class="page-header">

            <div class="page-header-title">

                <h4>

                    <i class="feather icon-clock"></i>

                    Withdraw History

                </h4>

            </div>

        </div>

        <div class="card">

            <div class="card-header">

                <div class="d-flex justify-content-between align-items-center">

                    <h5>

                        Withdrawal Requests

                    </h5>

                    <a href="{{ route('seller.wallet.withdraw.form') }}" class="btn btn-success">

                        <i class="feather icon-plus"></i>

                        New Withdraw

                    </a>

                </div>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover" id="withdrawTable">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Date</th>

                                <th>Amount</th>

                                <th>Payment Method</th>

                                <th>Status</th>

                                <th>Processed At</th>

                                <th>Action</th>

                            </tr>

                        </thead>

                        <tbody>
                            @if ($withdraws->count())

                                @foreach ($withdraws as $withdraw)
                                    <tr>

                                        <td>

                                            {{ $loop->iteration }}

                                        </td>

                                        <td>

                                            {{ $withdraw->created_at->format('d M Y') }}

                                            <br>

                                            <small>

                                                {{ $withdraw->created_at->format('h:i A') }}

                                            </small>

                                        </td>

                                        <td>

                                            ₹ {{ number_format($withdraw->amount, 2) }}

                                        </td>

                                        <td>

                                            {{ ucfirst($withdraw->payment_method) }}

                                        </td>

                                        <td>

                                            @if ($withdraw->status == 'pending')
                                                <span class="badge bg-warning">

                                                    Pending

                                                </span>
                                            @elseif($withdraw->status == 'approved')
                                                <span class="badge bg-primary">

                                                    Approved

                                                </span>
                                            @elseif($withdraw->status == 'paid')
                                                <span class="badge bg-success">

                                                    Paid

                                                </span>
                                            @else
                                                <span class="badge bg-danger">

                                                    Rejected

                                                </span>
                                            @endif

                                        </td>

                                        <td>

                                            {{ $withdraw->processed_at ? $withdraw->processed_at->format('d M Y') : '-' }}

                                        </td>

                                        <td>

                                            <a href="#" class="btn btn-primary btn-sm">

                                                <i class="feather icon-eye"></i>

                                            </a>

                                        </td>

                                    </tr>
                                @endforeach
                            @else
                                <tr>

                                    <td colspan="7" class="text-center">

                                        No Withdraw Request Found

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
        $(function() {

            $('#withdrawTable').DataTable({

                pageLength: 20,

                responsive: true,

                order: [
                    [1, 'desc']
                ]

            });

        });
    </script>
@endpush
