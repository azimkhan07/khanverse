@extends('buyer.layouts.app')

@section('title', 'Review Details')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">

                Review Details

            </h3>

            <small class="text-muted">

                View your submitted review.

            </small>

        </div>

        <a href="{{ route('buyer.reviews.index') }}" class="btn btn-secondary">

            <i class="ti-arrow-left"></i>

            Back

        </a>

    </div>

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="row">

                <div class="col-md-3 text-center">

                    @if(optional($review->seller)->profile_image)

                        <img src="{{ asset('storage/' . $review->seller->profile_image) }}"
                            class="rounded-circle border"
                            width="120"
                            height="120"
                            style="object-fit:cover;">

                    @else

                        <img src="{{ asset('admin/assets/images/avatar-4.jpg') }}"
                            class="rounded-circle border"
                            width="120"
                            height="120">

                    @endif

                    <h5 class="mt-3">

                        {{ optional($review->seller)->full_name ?? 'Seller' }}

                    </h5>

                    <small class="text-muted">

                        Seller

                    </small>

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

                                Project

                            </th>

                            <td>

                                {{ optional(optional($review->order)->project)->title ?? '-' }}

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

                                @for($i = 1; $i <= 5; $i++)

                                    @if($i <= $review->rating)

                                        <i class="ti-star text-warning"></i>

                                    @else

                                        <i class="ti-star text-muted"></i>

                                    @endif

                                @endfor

                                <strong class="ms-2">

                                    {{ $review->rating }}/5

                                </strong>

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

            <hr>

            <h5>

                Your Review

            </h5>

            <div class="border rounded p-4 bg-light">

                {!! nl2br(e($review->review)) !!}

            </div>

            <div class="text-end mt-4">

                <a href="{{ route('buyer.reviews.index') }}" class="btn btn-secondary">

                    <i class="ti-arrow-left"></i>

                    Back

                </a>

            </div>

        </div>

    </div>

</div>

@endsection
