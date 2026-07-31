<div class="card shadow-sm border-0 h-100">

    <div class="card-header bg-white border-0">

        <h5 class="mb-0">

            <i class="fas fa-wallet text-success me-2"></i>

            Wallet

        </h5>

    </div>

    <div class="card-body">

        <div class="mb-4">

            <small class="text-muted">

                Available Balance

            </small>

            <h2 class="fw-bold text-success">

                ₹ {{ number_format($wallet->balance ?? 0, 2) }}

            </h2>

        </div>

        <div class="mb-4">

            <small class="text-muted">

                Pending Balance

            </small>

            <h5 class="fw-bold text-warning">

                ₹ {{ number_format($wallet->pending_balance ?? 0, 2) }}

            </h5>

        </div>

        <a href="{{ route('buyer.wallet.index') }}" class="btn btn-primary w-100">

            View Wallet

        </a>

    </div>

</div>
