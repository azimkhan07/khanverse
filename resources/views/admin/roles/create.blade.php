<form id="roleForm" action="{{ route('admin.roles.store') }}" method="POST">

    @csrf

    <div class="modal-header">
        <h5>Add Role</h5>
    </div>

    <div class="modal-body">

        @include('admin.roles.form')

    </div>

    <div class="modal-footer">

        <button type="submit" class="btn btn-primary">
            Save
        </button>

    </div>

</form>