<div class="row">

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Total SEO Pages</h6>

                <h3>{{ \App\Models\SeoSetting::count() }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Active</h6>

                <h3>{{ \App\Models\SeoSetting::where('status', 1)->count() }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Inactive</h6>

                <h3>{{ \App\Models\SeoSetting::where('status', 0)->count() }}</h3>

            </div>

        </div>

    </div>

</div>
