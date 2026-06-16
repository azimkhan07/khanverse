<div class="modal-header">
    <h5 class="modal-title">Create Module</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form class="ajaxForm" action="{{ route('admin.modules.store') }}" method="POST">
    @csrf

    <div class="modal-body">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" placeholder="Users">
        </div>

        <div class="mb-3">
            <label>Slug</label>
            <input type="text" name="slug" class="form-control" placeholder="users">
        </div>

        <div class="mb-3">
            <label>Route</label>
            <input type="text" name="route" class="form-control" placeholder="admin.users.index">
        </div>

        <div class="mb-3">
            <label>View Path</label>
            <input type="text" name="view_path" class="form-control" placeholder="admin.dynamic.index">
        </div>

        <div class="mb-3">
            <label>Controller</label>
            <input type="text" name="controller" class="form-control"
                placeholder="App\Http\Controllers\Admin\UserController">
        </div>

        <div class="mb-3">
            <label>Panel</label>
            <select name="panel" class="form-control">
                <option value="admin">Admin</option>
                <option value="seller">Seller</option>
                <option value="buyer">Buyer</option>
                <option value="frontend">Frontend</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Roles</label>
            <select name="roles[]" class="form-control" multiple>
                <option value="admin">Admin</option>
                <option value="seller">Seller</option>
                <option value="buyer">Buyer</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save Module</button>
    </div>
</form>