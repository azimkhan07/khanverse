<div class="card shadow-sm mb-4">

    <div class="card-header">

        <h5 class="mb-0">

            Basic Information

        </h5>

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label class="form-label">

                Name

            </label>

            <input type="text" name="name" class="form-control" value="{{ old('name', $testimonial->name ?? '') }}"
                required>

        </div>

        <div class="mb-3">

            <label class="form-label">

                Designation

            </label>

            <input type="text" name="designation" class="form-control"
                value="{{ old('designation', $testimonial->designation ?? '') }}">

        </div>

        <div class="mb-3">

            <label class="form-label">

                Company

            </label>

            <input type="text" name="company" class="form-control"
                value="{{ old('company', $testimonial->company ?? '') }}">

        </div>

        <div class="mb-3">

            <label class="form-label">

                Rating

            </label>

            <select name="rating" class="form-control">

                @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ old('rating', $testimonial->rating ?? 5) == $i ? 'selected' : '' }}>

                        {{ $i }} Star

                    </option>
                @endfor

            </select>

        </div>

        <x-admin.editor id="review" name="review" label="Review" :value="$testimonial->review ?? ''" />

    </div>

</div>
