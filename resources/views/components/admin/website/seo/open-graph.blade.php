<div class="card shadow-sm mb-4">

    <div class="card-header">

        <h5>

            Open Graph

        </h5>

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>OG Title</label>

            <input type="text" name="og_title" class="form-control" value="{{ old('og_title', $seo->og_title ?? '') }}">

        </div>

        <div class="mb-3">

            <label>OG Description</label>

            <textarea name="og_description" rows="4" class="form-control">{{ old('og_description', $seo->og_description ?? '') }}</textarea>

        </div>

        <div class="mb-3">

            <label>OG Image</label>

            <input type="file" name="og_image" class="form-control">

        </div>

        @if (!empty($seo->og_image))
            <img src="{{ asset('storage/' . $seo->og_image) }}" class="img-thumbnail" width="120">
        @endif

    </div>

</div>
