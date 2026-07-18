<div class="card shadow-sm mb-4">

    <div class="card-header">

        <h5 class="mb-0">
            Basic Information
        </h5>

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>Title</label>

            <input type="text" name="title" class="form-control" value="{{ old('title', $page->title ?? '') }}"
                required>

        </div>

        <div class="mb-3">

            <label>Slug</label>

            <input type="text" class="form-control" value="{{ old('slug', $page->slug ?? '') }}" readonly>

            <small class="text-muted">
                Slug title se automatically generate hoga.
            </small>

        </div>

        <x-admin.editor id="description" name="description" label="Description" :value="$page->description ?? ''" />

    </div>

</div>
