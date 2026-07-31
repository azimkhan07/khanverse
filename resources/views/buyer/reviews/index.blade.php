@extends('seller.layouts.app')

@section('title', 'My Reviews')

@section('content')

    <div class="page-body">

        <div class="page-header">

            <div class="page-header-title">

                <h4>

                    <i class="feather icon-star"></i>

                    Customer Reviews

                </h4>

            </div>

        </div>
        <div class="row">

            <div class="col-md-4">

                <div class="card">

                    <div class="card-body text-center">

                        <h2 class="text-warning">

                            ⭐ {{ number_format($reviews->avg('rating'), 1) }}

                        </h2>

                        <small>

                            Average Rating

                        </small>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="card">

                    <div class="card-body text-center">

                        <h2>

                            {{ $reviews->count() }}

                        </h2>

                        <small>

                            Total Reviews

                        </small>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="card">

                    <div class="card-body text-center">

                        <h2 class="text-success">

                            {{ $reviews->where('rating', 5)->count() }}

                        </h2>

                        <small>

                            5 Star Reviews

                        </small>

                    </div>

                </div>

            </div>

        </div>

        <div class="card">

            <div class="card-header">

                <h5>

                    Review List

                </h5>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover" id="reviewTable">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Buyer</th>

                                <th>Order</th>

                                <th>Rating</th>

                                <th>Review</th>

                                <th>Date</th>

                                <th width="100">

                                    Action

                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @if ($reviews->count())

                                @foreach ($reviews as $review)
                                    <tr>

                                        <td>

                                            {{ $loop->iteration }}

                                        </td>

                                        <td>

                                            {{ $review->buyer->full_name ?? '-' }}

                                        </td>

                                        <td>

                                            #{{ $review->order_id }}

                                        </td>

                                        <td>

                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $review->rating)
                                                    <i class="feather icon-star text-warning"></i>
                                                @else
                                                    <i class="feather icon-star text-muted"></i>
                                                @endif
                                            @endfor

                                        </td>

                                        <td>

                                            {{ \Illuminate\Support\Str::limit($review->review, 50) }}

                                        </td>

                                        <td>

                                            {{ $review->created_at->format('d M Y') }}

                                        </td>

                                        <td>

                                            <a href="{{ route('seller.reviews.show', $review->id) }}"
                                                class="btn btn-primary btn-sm">

                                                <i class="feather icon-eye"></i>

                                            </a>

                                        </td>

                                    </tr>
                                @endforeach
                            @else
                                <tr>

                                    <td colspan="7" class="text-center">

                                        No Reviews Found

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

            $('#reviewTable').DataTable({

                pageLength: 20,

                responsive: true,

                order: [
                    [5, 'desc']
                ]

            });

        });
    </script>
@endpush
