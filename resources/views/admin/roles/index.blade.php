@extends('layouts.admin')

@section('title', 'Roles')

@section('content')

    <div class="card">

        <div class="card-header d-flex justify-content-between">

            <h5>Roles</h5>
            @if (hasPermission('roles.create'))
                <button class="btn btn-primary" id="addRoleBtn">
                    Add Role
                </button>
            @endif

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>

                <tbody id="roleTable">

                    @foreach ($roles as $role)
                        <tr>

                            <td>{{ $role->id }}</td>

                            <td>{{ $role->name }}</td>

                            <td>
                                @if (hasPermission('roles.edit'))
                                    <button class="btn btn-sm btn-info editRoleBtn"
                                        data-url="{{ route('admin.roles.edit', $role->id) }}">
                                        Edit
                                    </button>
                                @endif
                                @if (hasPermission('roles.delete'))
                                    <button class="btn btn-sm btn-danger deleteRoleBtn"
                                        data-url="{{ route('admin.roles.destroy', $role->id) }}">
                                        Delete
                                    </button>
                                @endif
                                @if (auth()->user()->roleData && auth()->user()->roleData->slug == 'admin')
                                    <a href="{{ route('admin.roles.permissions', $role->id) }}"
                                        class="btn btn-warning btn-sm">

                                        Permissions

                                    </a>
                                @endif

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

    {{-- Modal --}}
    <div class="modal fade" id="roleModal">
        <div class="modal-dialog">
            <div class="modal-content" id="roleModalContent"></div>
        </div>
    </div>

@endsection

@push('js')
    <script src="{{ asset('admin/js/roles/role.js') }}"></script>
@endpush
