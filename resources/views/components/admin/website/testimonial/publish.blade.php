<div class="card shadow-sm mb-4">

    <div class="card-header">

        <h5>

            Publish

        </h5>

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>

                Sort Order

            </label>

            <input
                type="number"
                name="sort_order"
                class="form-control"
                value="{{ old('sort_order',$testimonial->sort_order ?? 0) }}">

        </div>

        <div class="mb-3">

            <label>

                Status

            </label>

            <select
                name="status"
                class="form-control">

                <option
                    value="1"
                    {{ old('status',$testimonial->status ?? 1)==1?'selected':'' }}>

                    Active

                </option>

                <option
                    value="0"
                    {{ old('status',$testimonial->status ?? 1)==0?'selected':'' }}>

                    Inactive

                </option>

            </select>

        </div>

        <button
            type="submit"
            class="btn btn-success w-100">

            {{ isset($testimonial) ? 'Update Testimonial' : 'Save Testimonial' }}

        </button>

    </div>

</div>
