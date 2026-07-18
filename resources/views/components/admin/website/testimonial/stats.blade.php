<div class="row">

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Total Testimonials</h6>

                <h3>{{ \App\Models\Testimonial::count() }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Active</h6>

                <h3>{{ \App\Models\Testimonial::where('status', 1)->count() }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Inactive</h6>

                <h3>{{ \App\Models\Testimonial::where('status', 0)->count() }}</h3>

            </div>

        </div>

    </div>

</div>
