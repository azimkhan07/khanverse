<div class="card shadow-sm border-0 h-100">

    <div class="card-header bg-white border-0">

        <div class="d-flex justify-content-between align-items-center">

            <h5 class="mb-0">

                <i class="fas fa-star text-warning me-2"></i>

                Recent Reviews

            </h5>

            <a href="{{ route('buyer.reviews.index') }}" class="btn btn-sm btn-primary">

                View All

            </a>

        </div>

    </div>

    <div class="card-body">

        @forelse($reviews as $review)
            <div class="border-bottom pb-3 mb-3">

                <div class="d-flex justify-content-between">

                    <strong>

                        {{ $review->seller->user->name ?? '-' }}

                    </strong>

                    <span class="text-warning">

                        ⭐ {{ $review->rating }}/5

                    </span>

                </div>

                <small class="text-muted">

                    {{ Str::limit($review->review, 80) }}

                </small>

            </div>

        @empty

            <div class="text-center py-4">

                <i class="fas fa-star fa-2x text-muted mb-2"></i>

                <p class="text-muted mb-0">

                    No Reviews Yet

                </p>

            </div>
        @endforelse

    </div>

</div>
