@extends('layouts.admin')

@section('title', 'Permissions')

@section('content')

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">Permissions</h5>

            <button class="btn btn-primary" id="addPermissionBtn">
                Add Permission
            </button>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Module</th>
                        <th>Group</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="permissionTable">

                    @foreach ($permissions as $permission)
                        <tr>

                            <td>{{ $permission->id }}</td>

                            <td>{{ $permission->name }}</td>

                            <td>{{ $permission->slug }}</td>

                            <td>{{ $permission->module }}</td>

                            <td>{{ $permission->group }}</td>

                            <td>

                                @if ($permission->status)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif

                            </td>

                            <td>

                                <button class="btn btn-info btn-sm editPermissionBtn"
                                    data-url="{{ route('admin.permissions.edit', $permission->id) }}">
                                    Edit
                                </button>

                                <button class="btn btn-danger btn-sm deletePermissionBtn"
                                    data-url="{{ route('admin.permissions.destroy', $permission->id) }}">
                                    Delete
                                </button>

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

    <div class="modal fade" id="permissionModal">

        <div class="modal-dialog">

            <div class="modal-content" id="permissionModalContent"></div>

        </div>

    </div>

@endsection

@push('js')
    <script src="{{ asset('admin/js/permissions/permission.js') }}"></script>
@endpush
