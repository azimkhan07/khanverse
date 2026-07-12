<div class="row mb-3">

    <div class="col-md-3">

        <div class="card">

            <div class="card-body text-center">

                <h6>Total Devices</h6>

                <h3>

                    {{ $devices->total() }}

                </h3>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card">

            <div class="card-body text-center">

                <h6>Current Devices</h6>

                <h3>

                    {{ $devices->where('is_current_device', 1)->count() }}

                </h3>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card">

            <div class="card-body text-center">

                <h6>Desktop</h6>

                <h3>

                    {{ $devices->where('platform', 'Windows')->count() }}

                </h3>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card">

            <div class="card-body text-center">

                <h6>Mobile</h6>

                <h3>

                    {{ $devices->where('platform', 'Android')->count() }}

                </h3>

            </div>

        </div>

    </div>

</div>
