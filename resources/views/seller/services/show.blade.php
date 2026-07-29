@extends('seller.layouts.app')

@section('title', 'Service Details')

@section('content')

    <div class="page-body">

        <div class="page-header">

            <div class="page-header-title">

                <h4>

                    <i class="feather icon-briefcase"></i>

                    Service Details

                </h4>

            </div>

        </div>

        <div class="card">

            <div class="card-header">

                <div class="d-flex justify-content-between align-items-center">

                    <h5>

                        {{ $service->title }}

                    </h5>

                    <a href="{{ route('seller.services.index') }}" class="btn btn-primary">

                        <i class="feather icon-arrow-left"></i>

                        Back

                    </a>

                </div>

            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-4">

                        @if ($service->thumbnail)
                            <img src="{{ asset('storage/' . $service->thumbnail) }}" class="img-fluid rounded border">
                        @else
                            <img src="{{ asset('admin/assets/images/no-image.png') }}" class="img-fluid rounded border">
                        @endif

                    </div>

                    <div class="col-md-8">

                        <table class="table table-bordered">

                            <tr>

                                <th width="220">

                                    Category

                                </th>

                                <td>

                                    {{ $service->category->name ?? '-' }}

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Price

                                </th>

                                <td>

                                    ₹ {{ number_format($service->price, 2) }}

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Delivery Time

                                </th>

                                <td>

                                    {{ $service->delivery_days }} Days

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Revisions

                                </th>

                                <td>

                                    {{ $service->revisions }}

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Status

                                </th>

                                <td>

                                    @if ($service->status)
                                        <span class="badge bg-success">

                                            Active

                                        </span>
                                    @else
                                        <span class="badge bg-danger">

                                            Inactive

                                        </span>
                                    @endif

                                </td>

                            </tr>

                        </table>

                    </div>

                </div>
                <hr>

                <h5>

                    Description

                </h5>

                <div class="border rounded p-3">

                    {!! nl2br(e($service->description)) !!}

                </div>
                <hr>

                <h5>

                    Gallery

                </h5>

                <div class="row">

                    @if ($service->images->count())

                        @foreach ($service->images as $image)
                            <div class="col-md-3 mb-3">

                                <img src="{{ asset('storage/' . $image->image) }}" class="img-fluid rounded border">

                            </div>
                        @endforeach
                    @else
                        <div class="col-12">

                            <div class="alert alert-warning">

                                No Gallery Images Found

                            </div>

                        </div>

                    @endif

                </div>
                <div class="text-end mt-4">

                    <a href="{{ route('seller.services.edit', $service->id) }}" class="btn btn-warning">

                        <i class="feather icon-edit"></i>

                        Edit Service

                    </a>

                    <a href="{{ route('seller.services.gallery', $service->id) }}" class="btn btn-info">

                        <i class="feather icon-image"></i>

                        Manage Gallery

                    </a>

                </div>

            </div>

        </div>

    </div>

@endsection
