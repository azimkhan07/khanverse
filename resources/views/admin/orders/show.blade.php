@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')

    <div class="container-fluid">

        <div class="row">

            <div class="col-md-8">

                <div class="card mb-3">

                    <div class="card-header">

                        <h5>Order Information</h5>

                    </div>

                    <div class="card-body">

                        <table class="table">

                            <tr>

                                <th>Buyer</th>

                                <td>{{ $order->buyer?->user?->username }}</td>

                            </tr>

                            <tr>

                                <th>Seller</th>

                                <td>{{ $order->seller?->user?->username }}</td>

                            </tr>

                            <tr>

                                <th>Service</th>

                                <td>{{ $order->service?->title }}</td>

                            </tr>

                            <tr>

                                <th>Project</th>

                                <td>{{ $order->project?->title }}</td>

                            </tr>

                            <tr>

                                <th>Requirements</th>

                                <td>{{ $order->requirements }}</td>

                            </tr>

                            <tr>

                                <th>Delivery Date</th>

                                <td>{{ $order->delivery_date }}</td>

                            </tr>

                            <tr>

                                <th>Status</th>

                                <td>

                                    @include('components.admin.orders.status-badge', [
                                        'status' => $order->status,
                                    ])

                                </td>

                            </tr>

                        </table>

                    </div>

                </div>

                <div class="card">

                    <div class="card-header">

                        <h5>Project Attachments</h5>

                    </div>

                    <div class="card-body">

                        @if ($order->project)
                            <a href="{{ route('admin.projects.attachments.index', $order->project->id) }}"
                                class="btn btn-primary">

                                View Attachments

                            </a>
                        @else
                            -
                        @endif

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                @include('components.admin.orders.payment-card')

                @include('components.admin.orders.timeline')

            </div>

        </div>

    </div>

@endsection
