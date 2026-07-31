@extends('buyer.layouts.app')

@section('title', 'Write Review')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="mb-1">Write Review</h3>

                <small class="text-muted">
                    Share your experience with the seller.
                </small>

            </div>

            <a href="{{ route('buyer.reviews.index') }}" class="btn btn-secondary">

                <i class="ti-arrow-left"></i>

                Back

            </a>

        </div>

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <form action="{{ route('buyer.reviews.store') }}" method="POST">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label">

                            Order

                        </label>

                        <select name="order_id" class="form-select @error('order_id') is-invalid @enderror" required>

                            <option value="">

                                Select Order

                            </option>

                            @foreach ($orders as $order)
                                <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>

                                    #{{ $order->id }} -
                                    {{ optional($order->project)->title }}

                                </option>
                            @endforeach

                        </select>

                        @error('order_id')
                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>
                        @enderror

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Rating

                        </label>

                        <select name="rating" class="form-select @error('rating') is-invalid @enderror" required>

                            <option value="">Select Rating</option>

                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>

                                    {{ $i }} Star{{ $i > 1 ? 's' : '' }}

                                </option>
                            @endfor

                        </select>

                        @error('rating')
                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>
                        @enderror

                    </div>

                    <div class="mb-4">

                        <label class="form-label">

                            Review

                        </label>

                        <textarea name="review" rows="6" class="form-control @error('review') is-invalid @enderror"
                            placeholder="Write your experience..." required>{{ old('review') }}</textarea>

                        @error('review')
                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>
                        @enderror

                    </div>

                    <div class="text-end">

                        <button class="btn btn-primary">

                            <i class="ti-check"></i>
                            Submit Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
