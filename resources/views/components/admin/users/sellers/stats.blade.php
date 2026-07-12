<div class="row mb-3">

    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Total Sellers</h6>
                <h3>{{ $sellers->total() }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Available</h6>
                <h3>{{ \App\Models\Seller::where('available_for_work', 1)->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Verified</h6>
                <h3>{{ \App\Models\User::where('role', 'seller')->where('is_verified', 1)->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Banned</h6>
                <h3>{{ \App\Models\User::where('role', 'seller')->where('is_banned', 1)->count() }}</h3>
            </div>
        </div>
    </div>

</div>
