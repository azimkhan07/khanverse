<div class="card shadow-sm">

    <div class="card-header">

        Publish

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>Status</label>

            <select name="status" class="form-control">

                <option value="1" {{ old('status', $page->status ?? 1) == 1 ? 'selected' : '' }}>

                    Active

                </option>

                <option value="0" {{ old('status', $page->status ?? 1) == 0 ? 'selected' : '' }}>

                    Inactive

                </option>

            </select>

        </div>

        <button class="btn btn-success w-100">

            {{ isset($page) ? 'Update Page' : 'Create Page' }}

        </button>

    </div>

</div>
