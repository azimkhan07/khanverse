@extends('layouts.admin')

@section('title', 'Orders')

@section('content')

    <div class="container-fluid">

        <div class="card">

            <div class="card-header">

                <div class="row">

                    <div class="col-md-4">

                        <h4 class="mb-0">Orders</h4>

                    </div>

                    <div class="col-md-8">

                        <form>

                            <div class="row">

                                <div class="col-md-5">

                                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                        placeholder="Search Buyer / Seller / Service">

                                </div>

                                <div class="col-md-3">

                                    <select name="status" class="form-control">

                                        <option value="">All Status</option>

                                        <option value="pending">Pending</option>

                                        <option value="accepted">Accepted</option>

                                        <option value="in_progress">In Progress</option>

                                        <option value="completed">Completed</option>

                                        <option value="cancelled">Cancelled</option>

                                    </select>

                                </div>

                                <div class="col-md-2">

                                    <button class="btn btn-primary w-100">

                                        Search

                                    </button>

                                </div>

                                <div class="col-md-2">

                                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary w-100">

                                        Reset

                                    </a>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

            <div class="card-body table-responsive">

                <table class="table table-bordered table-hover">

                    <thead>

                        <tr>

                            <th>#</th>

                            <th>Buyer</th>

                            <th>Seller</th>

                            <th>Service</th>

                            <th>Project</th>

                            <th>Amount</th>

                            <th>Fee</th>

                            <th>Status</th>

                            <th>Delivery</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($orders as $order)
                            <tr>

                                <td>{{ $order->id }}</td>

                                <td>{{ $order->buyer?->user?->username }}</td>

                                <td>{{ $order->seller?->user?->username }}</td>

                                <td>{{ $order->service?->title }}</td>

                                <td>{{ $order->project?->title }}</td>

                                <td>₹{{ number_format($order->amount, 2) }}</td>

                                <td>₹{{ number_format($order->platform_fee, 2) }}</td>

                                <td>

                                    @include('components.admin.orders.status-badge', [
                                        'status' => $order->status,
                                    ])

                                </td>

                                <td>

                                    {{ $order->delivery_date }}

                                </td>

                                <td>

                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">

                                        View

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="10" class="text-center">

                                    No Orders Found

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

                {{ $orders->links() }}

            </div>

        </div>

    </div>

@endsection
