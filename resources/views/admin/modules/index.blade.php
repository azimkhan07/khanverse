@extends('layouts.admin')

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Modules</h5>

        <button class="btn btn-primary openModalBtn"
            data-url="{{ route('admin.modules.create') }}">
            Add Module
        </button>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Route</th>
                    <th>View Path</th>
                    <th>Controller</th>
                    <th>Panel</th>
                    <th>Status</th>
                    <th width="120">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($modules as $module)
                    <tr>
                        <td>{{ $module->id }}</td>
                        <td>{{ $module->name }}</td>
                        <td>{{ $module->slug }}</td>
                        <td>{{ $module->route }}</td>
                        <td>{{ $module->view_path }}</td>
                        <td>{{ $module->controller }}</td>
                        <td>{{ ucfirst($module->panel) }}</td>
                        <td>
                            @if($module->status)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary openModalBtn"
                                data-url="{{ route('admin.modules.edit', $module->id) }}">
                                Edit
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No modules found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $modules->links() }}
    </div>
</div>

<div class="modal fade" id="globalModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="globalModalContent"></div>
    </div>
</div>

@endsection