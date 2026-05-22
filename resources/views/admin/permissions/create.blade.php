<form id="permissionForm"
      action="{{ route('admin.permissions.store') }}"
      method="POST">

    @csrf

    <div class="modal-header">

        <h5>Add Permission</h5>

    </div>

    <div class="modal-body">

        @include('admin.permissions.form')

    </div>

    <div class="modal-footer">

        <button type="submit" class="btn btn-primary">
            Save
        </button>

    </div>

</form>