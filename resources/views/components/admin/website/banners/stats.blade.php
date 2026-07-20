<div class="row mb-4">

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Total Banners</h6>

                <h3>{{ \App\Models\Banner::count() }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Active</h6>

                <h3>{{ \App\Models\Banner::where('status', 1)->count() }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Inactive</h6>

                <h3>{{ \App\Models\Banner::where('status', 0)->count() }}</h3>

            </div>

        </div>

    </div>

</div>
