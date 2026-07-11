@extends('layouts.admin')

@section('content')
    <div class="page-header">

        <div class="page-header-title">
            <h4>Projects</h4>
        </div>

    </div>

    <div class="card">

        <div class="card-header">

            <div class="row">

                <div class="col-md-4">

                    <form>

                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search Project">

                    </form>

                </div>

                <div class="col-md-3">

                    <form>

                        <select name="status" onchange="this.form.submit()" class="form-control">

                            <option value="">All Status</option>

                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>
                                Draft
                            </option>

                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                Completed
                            </option>

                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>

                        </select>

                    </form>

                </div>

            </div>

        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover">

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Title</th>

                        <th>Buyer</th>

                        <th>Seller</th>

                        <th>Budget</th>

                        <th>Deadline</th>

                        <th>Status</th>

                        <th width="80">Action</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($projects as $project)
                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $project->title }}</td>

                            <td>{{ $project->buyer?->full_name ?? '-' }}</td>

                            <td>{{ $project->seller?->full_name ?? '-' }}</td>

                            <td>₹{{ number_format($project->budget, 2) }}</td>

                            <td>{{ $project->deadline }}</td>

                            <td>

                                @switch($project->status)
                                    @case('draft')
                                        <span class="badge bg-secondary">
                                            Draft
                                        </span>
                                    @break

                                    @case('active')
                                        <span class="badge bg-success">
                                            Active
                                        </span>
                                    @break

                                    @case('completed')
                                        <span class="badge bg-primary">
                                            Completed
                                        </span>
                                    @break

                                    @case('cancelled')
                                        <span class="badge bg-danger">
                                            Cancelled
                                        </span>
                                    @break
                                @endswitch

                            </td>

                            <td>

                                <a href="{{ route('admin.projects.show', $project->id) }}" class="btn btn-sm btn-info">

                                    <i class="ti-eye"></i>

                                </a>

                            </td>

                        </tr>

                        @empty

                            <tr>

                                <td colspan="8" class="text-center">

                                    No Projects Found

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

                {{ $projects->links() }}

            </div>

        </div>
    @endsection
