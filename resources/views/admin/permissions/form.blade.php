<div class="form-group mb-3">

    <label>Name</label>

    <input type="text" name="name" id="permissionName" class="form-control" value="{{ $permission->name ?? '' }}">

</div>

<div class="form-group mb-3">

    <label>Slug</label>

    <input type="text" name="slug" id="permissionSlug" class="form-control" value="{{ $permission->slug ?? '' }}">

</div>

<div class="form-group mb-3">

    <label>Module</label>

    <input type="text" name="module" class="form-control" placeholder="roles / permissions / menu"
        value="{{ $permission->module ?? '' }}">

</div>

<div class="form-group mb-3">

    <label>Group</label>

    <select name="group" class="form-control">

        <option value="">Select Group</option>

        <option value="admin" {{ isset($permission) && $permission->group == 'admin' ? 'selected' : '' }}>
            Admin
        </option>

        <option value="seller" {{ isset($permission) && $permission->group == 'seller' ? 'selected' : '' }}>
            Seller
        </option>

        <option value="buyer" {{ isset($permission) && $permission->group == 'buyer' ? 'selected' : '' }}>
            Buyer
        </option>

        <option value="frontend" {{ isset($permission) && $permission->group == 'frontend' ? 'selected' : '' }}>
            Frontend
        </option>

    </select>

</div>

<div class="form-group">

    <label>Status</label>

    <select name="status" class="form-control">

        <option value="1" {{ isset($permission) && $permission->status == 1 ? 'selected' : '' }}>
            Active
        </option>

        <option value="0" {{ isset($permission) && $permission->status == 0 ? 'selected' : '' }}>
            Inactive
        </option>

    </select>

</div>