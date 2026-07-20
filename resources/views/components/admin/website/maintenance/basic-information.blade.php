<div class="card shadow-sm mb-4">

    <div class="card-header">

        <h5 class="mb-0">

            Maintenance Information

        </h5>

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>Title</label>

            <input type="text" name="title" class="form-control" value="{{ old('title', $maintenance->title ?? '') }}">

        </div>

        <div class="mb-3">

            <label>Message</label>

            <x-admin.editor id="message" name="message" label="Message" :value="$maintenance->message ?? ''" />

        </div>

        <div class="mb-3">

            <label>Maintenance Image</label>

            <input type="file" name="image" class="form-control">

        </div>

        @if (!empty($maintenance->image))
            <img src="{{ asset('storage/' . $maintenance->image) }}" class="img-thumbnail" width="180">
        @endif

        <div class="row mt-3">

            <div class="col-md-6">

                <label>Button Text</label>

                <input type="text" name="button_text" class="form-control"
                    value="{{ old('button_text', $maintenance->button_text ?? '') }}">

            </div>

            <div class="col-md-6">

                <label>Button URL</label>

                <input type="url" name="button_url" class="form-control"
                    value="{{ old('button_url', $maintenance->button_url ?? '') }}">

            </div>

        </div>

        <div class="row mt-3">

            <div class="col-md-6">

                <label>Start Time</label>

                <input type="datetime-local" name="start_at" class="form-control"
                    value="{{ old('start_at', optional($maintenance->start_at)->format('Y-m-d\TH:i')) }}">

            </div>

            <div class="col-md-6">

                <label>End Time</label>

                <input type="datetime-local" name="end_at" class="form-control"
                    value="{{ old('end_at', optional($maintenance->end_at)->format('Y-m-d\TH:i')) }}">

            </div>

        </div>

    </div>

</div>
