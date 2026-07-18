<div class="card shadow-sm mb-4">

    <div class="card-header">

        <h5>

            Media

        </h5>

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>

                Profile Image

            </label>

            <input type="file" name="image" class="form-control">

        </div>

        @if (!empty($testimonial->image))
            <div class="mt-2">

                <img src="{{ asset('storage/' . $testimonial->image) }}" class="img-thumbnail" width="120">

            </div>
        @endif

    </div>

</div>
