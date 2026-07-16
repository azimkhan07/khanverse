<div class="card shadow-sm mb-4">

    <div class="card-header">

        Display Settings

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>Sort Order</label>

            <input type="number" name="sort_order" class="form-control"
                value="{{ old('sort_order', $homepage->sort_order ?? 0) }}">

        </div>

        <div class="mb-3">

            <label>Status</label>

            <select class="form-control" name="status">

                <option value="1" {{ old('status', $homepage->status ?? 1) == 1 ? 'selected' : '' }}>
                    Active
                </option>

                <option value="0" {{ old('status', $homepage->status ?? 1) == 0 ? 'selected' : '' }}>
                    Inactive
                </option>

            </select>

        </div>

    </div>

</div>
