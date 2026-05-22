@extends('layouts.admin')

@section('content')
    <div class="card">

        <div class="card-header">

            <h5>
                Assign Permissions : {{ $role->name }}
            </h5>

        </div>

        <div class="card-body">

            <form id="assignPermissionForm">

                @csrf

                <div class="row">

                    @foreach ($permissions as $module => $items)
                        <div class="col-md-4 mb-4">

                            <div class="border p-3 rounded">

                                <h6 class="mb-3">

                                    {{ ucfirst($module) }}

                                </h6>

                                @foreach ($items as $permission)
                                    <div class="form-check mb-2">

                                        <input type="checkbox" class="form-check-input" name="permissions[]"
                                            value="{{ $permission->id }}"
                                            {{ in_array($permission->id, $assignedPermissions) ? 'checked' : '' }}>

                                        <label class="form-check-label">

                                            {{ $permission->name }}

                                        </label>

                                    </div>
                                @endforeach

                            </div>

                        </div>
                    @endforeach

                </div>

                <button class="btn btn-primary">

                    Save Permissions

                </button>

            </form>

        </div>

    </div>
@endsection

@push('js')
    <script>
        $('#assignPermissionForm').submit(function(e) {

            e.preventDefault();

            $.ajax({

                url: "{{ route('admin.roles.permissions.store', $role->id) }}",

                type: 'POST',

                data: $(this).serialize(),

                success: function(response) {

                    toastr.success(response.message);
                    setTimeout(() => {
                        window.history.back();
                    }, 3000);
                }

            });

        });
    </script>
@endpush
