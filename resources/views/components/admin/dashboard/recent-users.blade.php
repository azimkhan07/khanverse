<div class="card shadow border-0 mb-4">

    <div class="card-header bg-white d-flex justify-content-between align-items-center">

        <h5 class="mb-0">

            <i class="fas fa-users text-primary"></i>

            Recent Users

        </h5>

        <a href="#" class="text-decoration-none">

            View All

        </a>

    </div>

    <div class="card-body">

        {{-- Recent Sellers --}}

        <h6 class="text-muted mb-3">

            Recent Sellers

        </h6>

        @forelse($recentSellers ?? [] as $seller)

            <div class="d-flex align-items-center justify-content-between mb-3">

                <div class="d-flex align-items-center">

                    <img
                        src="https://ui-avatars.com/api/?name={{ urlencode($seller->name) }}&background=0D8ABC&color=fff"
                        class="rounded-circle me-3"
                        width="45"
                        height="45">

                    <div>

                        <h6 class="mb-0">

                            {{ $seller->name }}

                        </h6>

                        <small class="text-muted">

                            Joined {{ $seller->created_at->diffForHumans() }}

                        </small>

                    </div>

                </div>

                <span class="badge bg-success">

                    Seller

                </span>

            </div>

        @empty

            <p class="text-muted">

                No Seller Found

            </p>

        @endforelse

        <hr>

        {{-- Recent Buyers --}}

        <h6 class="text-muted mb-3">

            Recent Buyers

        </h6>

        @forelse($recentBuyers ?? [] as $buyer)

            <div class="d-flex align-items-center justify-content-between mb-3">

                <div class="d-flex align-items-center">

                    <img
                        src="https://ui-avatars.com/api/?name={{ urlencode($buyer->name) }}&background=6C63FF&color=fff"
                        class="rounded-circle me-3"
                        width="45"
                        height="45">

                    <div>

                        <h6 class="mb-0">

                            {{ $buyer->name }}

                        </h6>

                        <small class="text-muted">

                            Joined {{ $buyer->created_at->diffForHumans() }}

                        </small>

                    </div>

                </div>

                <span class="badge bg-primary">

                    Buyer

                </span>

            </div>

        @empty

            <p class="text-muted">

                No Buyer Found

            </p>

        @endforelse

    </div>

</div>
