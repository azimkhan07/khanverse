<div class="card shadow-sm">

    <div class="card-header">

        Maintenance Mode

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>Status</label>

            <select name="status" class="form-control">

                <option value="0" {{ old('status', $maintenance->status ?? 0) == 0 ? 'selected' : '' }}>

                    Disabled

                </option>

                <option value="1" {{ old('status', $maintenance->status ?? 0) == 1 ? 'selected' : '' }}>

                    Enabled

                </option>

            </select>

        </div>

        <button class="btn btn-success w-100">

            Save Settings

        </button>

    </div>

</div>
