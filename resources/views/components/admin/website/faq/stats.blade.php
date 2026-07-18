<div class="row">

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Total FAQ</h6>

                <h3>{{ \App\Models\Faq::count() }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Active FAQ</h6>

                <h3>{{ \App\Models\Faq::where('status', 1)->count() }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Inactive FAQ</h6>

                <h3>{{ \App\Models\Faq::where('status', 0)->count() }}</h3>

            </div>

        </div>

    </div>

</div>
