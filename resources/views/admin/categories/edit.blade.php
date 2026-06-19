<div class="modal-header">

    <h5>Edit Category</h5>

</div>

<form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="ajaxForm">

    @csrf

    <div class="modal-body">

        <div class="mb-3">

            <label>Name</label>

            <input type="text" name="name" value="{{ $category->name }}" class="form-control">

        </div>

        <div class="mb-3">

            <label>Slug</label>

            <input type="text" name="slug" value="{{ $category->slug }}" class="form-control">

        </div>

        <div class="mb-3">

            <label>Icon</label>

            <input type="text" name="icon" value="{{ $category->icon }}" class="form-control">

        </div>

    </div>

    <div class="modal-footer">

        <button type="submit" class="btn btn-primary">

            Update Category

        </button>

    </div>

</form>