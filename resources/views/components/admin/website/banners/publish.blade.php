<div class="card shadow-sm mb-4">

    <div class="card-header">

        Publish

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>Status</label>

            <select name="status" class="form-control">

                <option value="1" {{ old('status', $banner->status ?? 1) == 1 ? 'selected' : '' }}>
                    Active
                </option>

                <option value="0" {{ old('status', $banner->status ?? 1) == 0 ? 'selected' : '' }}>
                    Inactive
                </option>

            </select>

        </div>

        <button class="btn btn-success w-100">

            {{ isset($banner) ? 'Update Banner' : 'Save Banner' }}

        </button>

    </div>

</div>
