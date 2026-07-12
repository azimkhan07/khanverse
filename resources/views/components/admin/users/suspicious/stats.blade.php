<div class="row mb-3">

    <div class="col-md-3">

        <div class="card">

            <div class="card-body text-center">

                <h6>Same IP Accounts</h6>

                <h3>{{ $users->count() }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card">

            <div class="card-body text-center">

                <h6>Banned</h6>

                <h3>

                    {{ $users->where('is_banned', 1)->count() }}

                </h3>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card">

            <div class="card-body text-center">

                <h6>Not Verified</h6>

                <h3>

                    {{ $users->where('is_verified', 0)->count() }}

                </h3>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card">

            <div class="card-body text-center">

                <h6>Flagged Users</h6>

                <h3>

                    {{ $users->count() }}

                </h3>

            </div>

        </div>

    </div>

</div>
