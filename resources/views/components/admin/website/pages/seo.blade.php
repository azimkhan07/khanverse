<div class="card shadow-sm mb-4">

    <div class="card-header">

        SEO

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>Meta Title</label>

            <input type="text" name="meta_title" class="form-control"
                value="{{ old('meta_title', $page->meta_title ?? '') }}">

        </div>

        <div class="mb-3">

            <label>Meta Keywords</label>

            <textarea name="meta_keywords" class="form-control" rows="3">{{ old('meta_keywords', $page->meta_keywords ?? '') }}</textarea>

        </div>

        <div class="mb-3">

            <label>Meta Description</label>

            <textarea name="meta_description" class="form-control" rows="4">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>

        </div>

    </div>

</div>
