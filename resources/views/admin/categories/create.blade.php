<div class="modal-header">

    <h5 class="modal-title">
        Create Category
    </h5>

    <button type="button" class="btn-close" data-bs-dismiss="modal">
    </button>

</div>

<form action="{{ route('admin.categories.store') }}" method="POST" class="ajaxForm">

    @csrf

    <div class="modal-body">

        <div class="mb-3">

            <label>Name</label>

            <input type="text" name="name" class="form-control">

        </div>

        <div class="mb-3">

            <label>Slug</label>

            <input type="text" name="slug" class="form-control">

        </div>

        <div class="mb-3">

            <label>Icon</label>

            <input type="text" name="icon" class="form-control">

        </div>

    </div>

    <div class="modal-footer">

        <button type="submit" class="btn btn-primary">

            Save Category

        </button>

    </div>

</form>