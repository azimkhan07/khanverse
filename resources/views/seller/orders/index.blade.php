@extends('seller.layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="mb-1">

                    My Orders

                </h3>

                <small class="text-muted">

                    Manage all your client orders.

                </small>

            </div>

        </div>

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Project</th>

                                <th>Buyer</th>

                                <th>Amount</th>

                                <th>Status</th>

                                <th>Delivery</th>

                                <th>Action</th>

                            </tr>

                        </thead>

                        <tbody>
                            @forelse($orders as $order)
                                <tr>

                                    <td>

                                        #{{ $order->id }}

                                    </td>

                                    <td>

                                        {{ optional($order->project)->title ?? '-' }}

                                    </td>

                                    <td>

                                        {{ optional($order->buyer)->name ?? '-' }}

                                    </td>

                                    <td>

                                        ₹{{ number_format($order->amount, 2) }}

                                    </td>

                                    <td>

                                        @switch($order->status)
                                            @case('pending')
                                                <span class="badge bg-warning">

                                                    Pending

                                                </span>
                                            @break

                                            @case('active')
                                                <span class="badge bg-primary">

                                                    Active

                                                </span>
                                            @break

                                            @case('delivered')
                                                <span class="badge bg-info">

                                                    Delivered

                                                </span>
                                            @break

                                            @case('completed')
                                                <span class="badge bg-success">

                                                    Completed

                                                </span>
                                            @break

                                            @case('cancelled')
                                                <span class="badge bg-danger">

                                                    Cancelled

                                                </span>
                                            @break
                                        @endswitch

                                    </td>

                                    <td>

                                        {{ $order->delivery_date ?? '-' }}

                                    </td>

                                    <td>

                                        <a href="{{ route('seller.orders.show', $order->id) }}"
                                            class="btn btn-sm btn-primary">

                                            <i class="ti-eye"></i>

                                            View

                                        </a>

                                    </td>

                                </tr>

                                @empty

                                <tr>

                                    <td colspan="7" class="text-center py-5">

                                        No Orders Found

                                    </td>

                                </tr>
                            @endforelse
                        </tbody>

                    </table>

                </div>

                <div class="mt-3">

                    {{ $orders->links() }}

                </div>

            </div>

        </div>

    </div>
@endsection
