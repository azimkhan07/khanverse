<form id="roleForm" action="{{ route('admin.roles.update', $role->id) }}" method="POST">

    @csrf
    @method('PUT')

    <div class="modal-header">
        <h5>Edit Role</h5>
    </div>

    <div class="modal-body">

        @include('admin.roles.form')

    </div>

    <div class="modal-footer">

        <button type="submit" class="btn btn-primary">
            Update
        </button>

    </div>

</form>