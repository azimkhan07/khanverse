<div class="modal-header">

    <h5 class="modal-title">
        Create Service
    </h5>

    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

</div>

<form action="{{ route('seller.services.store') }}" method="POST" class="ajaxForm" enctype="multipart/form-data">

    @csrf

    <div class="modal-body">

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control">
        </div>

        <div class="mb-3">
            <label>Slug</label>
            <input type="text" name="slug" class="form-control">
        </div>

        <div class="mb-3">
            <label>Category</label>

            <select name="category_id" class="form-control">

                <option value="">
                    Select Category
                </option>

                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">
                        {{ $category->name }}
                    </option>
                @endforeach

            </select>

        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" rows="4" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Price</label>
            <input type="number" step="0.01" name="price" class="form-control">
        </div>

        <div class="mb-3">
            <label>Delivery Days</label>
            <input type="number" name="delivery_days" class="form-control">
        </div>

        <div class="mb-3">
            <label>Revisions</label>
            <input type="number" name="revisions" value="0" class="form-control">
        </div>

        <div class="mb-3">
            <label>Thumbnail</label>
            <input type="file" name="thumbnail" class="form-control imageInput">
            <div class="mt-2 imagePreviewWrapper d-none">
                <img src="" class="img-thumbnail imagePreview" width="150">
                <br>
                <button type="button" class="btn btn-sm btn-danger mt-2 removeImageBtn">
                    Remove
                </button>
            </div>
        </div>

        <div class="mb-3">

            <label>Status</label>

            <select name="status" class="form-control">

                <option value="draft">Draft</option>
                <option value="active">Active</option>
                <option value="paused">Paused</option>

            </select>

        </div>

    </div>

    <div class="modal-footer">

        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

            Close

        </button>

        <button type="submit" class="btn btn-primary">

            Save Service

        </button>

    </div>

</form>
