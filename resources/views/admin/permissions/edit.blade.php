<form id="permissionForm" action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">

    @csrf
    @method('PUT')

    <div class="modal-header">

        <h5>Edit Permission</h5>

    </div>

    <div class="modal-body">

        @include('admin.permissions.form')

    </div>

    <div class="modal-footer">

        <button type="submit" class="btn btn-primary">
            Update
        </button>

    </div>

</form>