@extends('buyer.layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">My Reviews</h3>
                <small class="text-muted"> Reviews you have submitted to sellers. </small>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Project</th>
                                <th>Seller</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Date</th>
                                <th width="100"> Action </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                                <tr>
                                    <td> {{ $loop->iteration + ($reviews->currentPage() - 1) * $reviews->perPage() }}</td>
                                    <td> {{ optional(optional($review->order)->project)->title ?? '-' }} </td>
                                    <td>{{ optional($review->seller)->full_name ?? '-' }} </td>
                                    <td>
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->rating)
                                                <i class="ti-star text-warning"></i>
                                            @else
                                                <i class="ti-star text-muted"></i>
                                            @endif
                                        @endfor
                                        <br>
                                        <small> {{ $review->rating }}/5 </small>
                                    </td>
                                    <td> {{ Str::limit($review->review, 60) }} </td>
                                    <td> {{ $review->created_at->format('d M Y') }} </td>
                                    <td> <a href="{{ route('buyer.reviews.show', $review->id) }}" class="btn btn-sm btn-primary"><i class="ti-eye"></i></a> </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="ti-star display-4 text-muted"></i>

                                        <h5 class="mt-3"> No Reviews Found </h5>
                                        <p class="text-muted mb-0">
                                            You haven't submitted any reviews yet.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $reviews->links() }}
        </div>
    </div>
@endsection
