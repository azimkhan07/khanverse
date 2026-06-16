<div class="modal-header">
    <h5 class="modal-title">Edit Module</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form class="ajaxForm" action="{{ route('admin.modules.update', $module->id) }}" method="POST">
    @csrf

    <div class="modal-body">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $module->name }}">
        </div>

        <div class="mb-3">
            <label>Slug</label>
            <input type="text" name="slug" class="form-control" value="{{ $module->slug }}">
        </div>

        <div class="mb-3">
            <label>Route</label>
            <input type="text" name="route" class="form-control" value="{{ $module->route }}">
        </div>

        <div class="mb-3">
            <label>View Path</label>
            <input type="text" name="view_path" class="form-control" value="{{ $module->view_path }}">
        </div>

        <div class="mb-3">
            <label>Controller</label>
            <input type="text" name="controller" class="form-control" value="{{ $module->controller }}">
        </div>

        <div class="mb-3">
            <label>Panel</label>
            <select name="panel" class="form-control">
                <option value="admin" @selected($module->panel == 'admin')>Admin</option>
                <option value="seller" @selected($module->panel == 'seller')>Seller</option>
                <option value="buyer" @selected($module->panel == 'buyer')>Buyer</option>
                <option value="frontend" @selected($module->panel == 'frontend')>Frontend</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Roles</label>
            @php
                $roles = $module->roles ?? [];
            @endphp

            <select name="roles[]" class="form-control" multiple>
                <option value="admin" @selected(in_array('admin', $roles))>Admin</option>
                <option value="seller" @selected(in_array('seller', $roles))>Seller</option>
                <option value="buyer" @selected(in_array('buyer', $roles))>Buyer</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="1" @selected($module->status == 1)>Active</option>
                <option value="0" @selected($module->status == 0)>Inactive</option>
            </select>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update Module</button>
    </div>
</form>