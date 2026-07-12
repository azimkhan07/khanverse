<div class="row mb-3">

    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Total Buyers</h6>
                <h3>{{ $buyers->total() }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Verified</h6>
                <h3>{{ \App\Models\User::where('role', 'buyer')->where('is_verified', 1)->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Active</h6>
                <h3>{{ \App\Models\User::where('role', 'buyer')->where('status', 1)->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Banned</h6>
                <h3>{{ \App\Models\User::where('role', 'buyer')->where('is_banned', 1)->count() }}</h3>
            </div>
        </div>
    </div>

</div>
