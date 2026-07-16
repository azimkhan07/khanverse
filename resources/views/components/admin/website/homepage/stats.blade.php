<div class="row mb-4">

    <div class="col-md-3">

        <div class="card shadow-sm">

            <div class="card-body text-center">

                <h6>Total Sections</h6>

                <h3>{{ $sections->count() }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card shadow-sm">

            <div class="card-body text-center">

                <h6>Active</h6>

                <h3>{{ $sections->where('status', 1)->count() }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card shadow-sm">

            <div class="card-body text-center">

                <h6>Inactive</h6>

                <h3>{{ $sections->where('status', 0)->count() }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card shadow-sm">

            <div class="card-body text-center">

                <h6>Last Updated</h6>

                <h5>

                    {{ optional($sections->first())->updated_at?->format('d M Y') ?? '-' }}

                </h5>

            </div>

        </div>

    </div>

</div>
