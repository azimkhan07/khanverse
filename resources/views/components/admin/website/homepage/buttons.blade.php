<div class="card shadow-sm mb-4">

    <div class="card-header">

        Buttons

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>Button Text</label>

            <input type="text" name="button_text" class="form-control"
                value="{{ old('button_text', $homepage->button_text ?? '') }}">

        </div>

        <div class="mb-3">

            <label>Button URL</label>

            <input type="text" name="button_url" class="form-control"
                value="{{ old('button_url', $homepage->button_url ?? '') }}">

        </div>

    </div>

</div>
