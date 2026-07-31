@extends('buyer.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('buyer.orders.index') }}" class="btn btn-light btn-sm mb-2">
                    <i class="ti-arrow-left"></i>
                    Back to Orders
                </a>
                <h3> Order #{{ $order->id }} </h3>
            </div>

            <div>
                @switch($order->status)
                    @case('pending')
                        <span class="badge bg-warning"> Pending </span>
                    @break

                    @case('active')
                        <span class="badge bg-primary"> Active </span>
                    @break

                    @case('delivered')
                        <span class="badge bg-info"> Delivered </span>
                    @break

                    @case('completed')
                        <span class="badge bg-success"> Completed </span>
                    @break

                    @case('cancelled')
                        <span class="badge bg-danger"> Cancelled </span>
                    @break
                @endswitch
            </div>
        </div>

        <div class="col-lg-6">

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header">
                    Service Provider Details
                </div>

                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="160"> Name </th>
                            <td> {{ optional($order->seller)->full_name }} </td>
                        </tr>
                        <tr>
                            <th> Email </th>
                            <td> {{ optional(optional($order->seller)->user)->email }} </td>
                        </tr>

                        <tr>
                            <th> Status </th>
                            <td> {{ ucfirst($order->status) }} </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header">
            Project Details
        </div>

        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th width="180"> Project </th>
                    <td> {{ optional($order->project)->title }} </td>
                </tr>
                <tr>
                    <th> Service </th>
                    <td> {{ optional($order->service)->title }} </td>
                </tr>
                <tr>
                    <th> Budget </th>
                    <td> ₹{{ number_format(optional($order->project)->budget, 2) }} </td>
                </tr>
                <tr>
                    <th> Deadline </th>
                    <td> {{ optional($order->project)->deadline }} </td>
                </tr>
                <tr>
                    <th> Description </th>
                    <td> {!! nl2br(e(optional($order->project)->description)) !!} </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header">
            Payment Summary
        </div>

        <div class="card-body">
            <table class="table">
                <tr>
                    <th> Order Amount </th>
                    <td class="text-end"> ₹{{ number_format($order->amount, 2) }} </td>
                </tr>
                <tr>
                    <th> Platform Fee </th>
                    <td class="text-end text-danger"> - ₹{{ number_format($order->amount, 2) }} </td>
                </tr>
                <tr>
                    <th> Total Paid </th>
                    <td class="text-end fw-bold text-success">
                        ₹{{ number_format($order->amount - $order->platform_fee, 2) }} </td>
                </tr>
                <tr>
                    <th> Delivery Date </th>
                    <td class="text-end"> {{ $order->delivery_date ?? '-' }} </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header">

            Project Workspace

        </div>

        <div class="card-body">
            <div class="row text-center">
                <div class="col-lg-4 mb-3">
                    <a href="{{ route('buyer.projects.show', $order->project_id) }}" class="btn btn-primary w-100">
                        <i class="ti-briefcase mr-1"></i>
                        Open Workspace
                    </a>
                </div>

                <div class="col-lg-4 mb-3">
                    <a href="{{ route('buyer.projects.attachments', $order->project_id) }}" class="btn btn-info w-100">
                        <i class="ti-folder mr-1"></i>
                        View Project File
                    </a>
                </div>

                <div class="col-lg-4 mb-3">
                    <button class="btn btn-success w-100" disabled>
                        <i class="ti-comments mr-1"></i>
                        Chat (Coming Soon)
                    </button>
                </div>

                @if($order->status == 'completed' && !$order->review)
                    <div class="col-lg-4 mb-3">
                        <a href="{{ route('buyer.reviews.create',$order->id) }}" class="btn btn-primary w-100">
                            <i class="ti-star mr-1"></i>
                            Add Review
                        </a>
                    </div>
                @endif
            </div>

            <hr>

            <small class="text-muted">

                Everything related to this order (chat, file sharing, delivery, revisions)
                will be managed from the Project Workspace.

            </small>

        </div>

    </div>
@endsection
