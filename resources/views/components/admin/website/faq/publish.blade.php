<div class="card shadow-sm mb-4">

    <div class="card-header">

        <h5 class="mb-0">

            Publish

        </h5>

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label class="form-label">

                Sort Order

            </label>

            <input
                type="number"
                name="sort_order"
                class="form-control"
                value="{{ old('sort_order', $faq->sort_order ?? 0) }}">

        </div>

        <div class="mb-3">

            <label class="form-label">

                Status

            </label>

            <select
                name="status"
                class="form-control">

                <option
                    value="1"
                    {{ old('status', $faq->status ?? 1) == 1 ? 'selected' : '' }}>

                    Active

                </option>

                <option
                    value="0"
                    {{ old('status', $faq->status ?? 1) == 0 ? 'selected' : '' }}>

                    Inactive

                </option>

            </select>

        </div>

        <button
            type="submit"
            class="btn btn-success w-100">

            {{ isset($faq) ? 'Update FAQ' : 'Save FAQ' }}

        </button>

    </div>

</div>
