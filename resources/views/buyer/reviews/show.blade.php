@extends('seller.layouts.app')

@section('title', 'Review Details')

@section('content')

    <div class="page-body">

        <div class="page-header">

            <div class="page-header-title">

                <h4>

                    <i class="feather icon-star"></i>

                    Review Details

                </h4>

            </div>

        </div>

        <div class="card">

            <div class="card-header">

                <div class="d-flex justify-content-between align-items-center">

                    <h5>

                        Customer Review

                    </h5>

                    <a href="{{ route('seller.reviews.index') }}" class="btn btn-primary">

                        <i class="feather icon-arrow-left"></i>

                        Back

                    </a>

                </div>

            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 text-center">

                        @if ($review->buyer->profile_image)
                            <img src="{{ asset('storage/' . $review->buyer->profile_image) }}"
                                class="img-fluid rounded-circle border" style="width:120px;height:120px;object-fit:cover;">
                        @else
                            <img src="{{ asset('admin/assets/images/avatar-4.jpg') }}"
                                class="img-fluid rounded-circle border" style="width:120px;height:120px;object-fit:cover;">
                        @endif

                        <h5 class="mt-3">

                            {{ $review->buyer->full_name }}

                        </h5>

                    </div>

                    <div class="col-md-9">

                        <table class="table table-bordered">

                            <tr>

                                <th width="220">

                                    Order ID

                                </th>

                                <td>

                                    #{{ $review->order_id }}

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Review Date

                                </th>

                                <td>

                                    {{ $review->created_at->format('d M Y h:i A') }}

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Rating

                                </th>

                                <td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            <i class="feather icon-star text-warning"></i>
                                        @else
                                            <i class="feather icon-star text-muted"></i>
                                        @endif
                                    @endfor

                                    &nbsp;

                                    <strong>

                                        {{ $review->rating }}/5

                                    </strong>

                                </td>

                            </tr>

                        </table>

                    </div>

                </div>
                <hr>

                <h5>

                    Review Message

                </h5>

                <div class="border rounded p-4 bg-light">

                    {!! nl2br(e($review->review)) !!}

                </div>
                <div class="text-end mt-4">

                    <a href="{{ route('seller.reviews.index') }}" class="btn btn-secondary">

                        Back

                    </a>

                </div>

            </div>

        </div>

    </div>

@endsection
