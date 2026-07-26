@extends('seller.layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h3 class="mb-1">Projects</h3>
                <p class="text-muted mb-0">
                    Manage all assigned projects.
                </p>
            </div>

        </div>

        <!-- Stats -->
        <div class="row">

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <h6 class="text-muted">Total Projects</h6>

                        <h3 class="mb-0">
                            {{ $projects->total() }}
                        </h3>

                    </div>

                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <h6 class="text-muted">Open</h6>

                        <h3 class="text-primary">
                            {{ $projects->where('status', 'open')->count() }}
                        </h3>

                    </div>

                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <h6 class="text-muted">In Progress</h6>

                        <h3 class="text-warning">
                            {{ $projects->where('status', 'in_progress')->count() }}
                        </h3>

                    </div>

                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <h6 class="text-muted">Completed</h6>

                        <h3 class="text-success">
                            {{ $projects->where('status', 'completed')->count() }}
                        </h3>

                    </div>

                </div>
            </div>

        </div>

        <!-- Search & Filter -->
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-body">

                <form method="GET" action="{{ route('seller.projects.index') }}">

                    <div class="row">

                        <div class="col-md-4 mb-2">

                            <input type="text" name="search" class="form-control" placeholder="Search project..."
                                value="{{ request('search') }}">

                        </div>

                        <div class="col-md-3 mb-2">

                            <select name="status" class="form-control">

                                <option value="">All Status</option>

                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>
                                    Open
                                </option>

                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>
                                    In Progress
                                </option>

                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>

                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled
                                </option>

                            </select>

                        </div>

                        <div class="col-md-2 mb-2">

                            <button class="btn btn-primary w-100">

                                <i class="ti-search"></i>
                                Search

                            </button>

                        </div>

                        <div class="col-md-2 mb-2">

                            <a href="{{ route('seller.projects.index') }}" class="btn btn-secondary w-100">

                                Reset

                            </a>

                        </div>

                    </div>

                </form>

            </div>

        </div>


        <!-- Projects Table -->

        <div class="card shadow-sm border-0">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>#</th>

                                <th>Project</th>

                                <th>Buyer</th>

                                <th>Budget</th>

                                <th>Deadline</th>

                                <th>Status</th>

                                <th width="120">Action</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($projects as $project)
                                <tr>

                                    <td>
                                        {{ $loop->iteration + ($projects->currentPage() - 1) * $projects->perPage() }}
                                    </td>

                                    <td>

                                        <div>

                                            <strong>
                                                {{ $project->title }}
                                            </strong>

                                            <br>

                                            <small class="text-muted">
                                                {{ Str::limit($project->description, 50) }}
                                            </small>

                                        </div>

                                    </td>

                                    <td>

                                        {{ optional($project->buyer)->full_name ?? 'N/A' }}

                                    </td>

                                    <td>

                                        ${{ number_format($project->budget, 2) }}

                                    </td>

                                    <td>

                                        {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}

                                    </td>

                                    <td>

                                        @switch($project->status)
                                            @case('open')
                                                <span class="badge bg-primary">
                                                    Open
                                                </span>
                                            @break

                                            @case('in_progress')
                                                <span class="badge bg-warning text-dark">
                                                    In Progress
                                                </span>
                                            @break

                                            @case('completed')
                                                <span class="badge bg-success">
                                                    Completed
                                                </span>
                                            @break

                                            @case('cancelled')
                                                <span class="badge bg-danger">
                                                    Cancelled
                                                </span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($project->status) }}
                                                </span>
                                        @endswitch

                                    </td>

                                    <td>

                                        <a href="{{ route('seller.projects.show', $project->id) }}"
                                            class="btn btn-sm btn-primary">

                                            <i class="ti-eye"></i>

                                        </a>

                                    </td>

                                </tr>

                                @empty

                                    <tr>

                                        <td colspan="7" class="text-center py-5">

                                            <img src="{{ asset('assets/images/no-data.svg') }}" width="120" class="mb-3"
                                                onerror="this.style.display='none'">

                                            <h5>No Projects Found</h5>

                                            <p class="text-muted mb-0">
                                                No projects have been assigned yet.
                                            </p>

                                        </td>

                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <div class="mt-4">

                {{ $projects->links() }}

            </div>

        </div>
    </div>
@endsection

