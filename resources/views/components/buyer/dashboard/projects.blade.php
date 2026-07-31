<div class="dashboard-table-card">

    <div class="dashboard-table-header">

        <div>

            <h5>

                <i class="fas fa-project-diagram me-2"></i>

                Active Projects

            </h5>

        </div>

        <a href="{{ route('buyer.projects.index') }}" class="btn btn-primary btn-sm rounded-pill px-4">

            View All

        </a>

    </div>

    <div class="table-responsive">

        <table class="table align-middle dashboard-table">

            <thead>

                <tr>

                    <th>Project</th>

                    <th>Seller</th>

                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

                @forelse($projects as $project)
                    <tr>

                        <td>

                            {{ $project->service->title ?? '-' }}

                        </td>

                        <td>

                            {{ $project->seller->full_name ?? '-' }}

                        </td>

                        <td>

                            <span class="badge bg-info">

                                {{ ucfirst($project->status) }}

                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="3" class="text-center">

                            No Active Projects

                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>
