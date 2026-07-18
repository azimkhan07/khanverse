<div class="row">

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Total Pages</h6>

                <h3>{{ \App\Models\Page::count() }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Active Pages</h6>

                <h3>{{ \App\Models\Page::where('status',1)->count() }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Inactive Pages</h6>

                <h3>{{ \App\Models\Page::where('status',0)->count() }}</h3>

            </div>

        </div>

    </div>

</div>
