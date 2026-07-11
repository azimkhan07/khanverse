<div class="card shadow-sm border-0">

    <div class="card-header">

        <h5 class="mb-0">
            <i class="ti-briefcase"></i>
            Project Information
        </h5>

    </div>

    <div class="card-body">

        <table class="table table-borderless">

            <tr>
                <th width="180">Title</th>
                <td>{{ $project->title }}</td>
            </tr>

            <tr>
                <th>Slug</th>
                <td>{{ $project->slug }}</td>
            </tr>

            <tr>
                <th>Budget</th>
                <td>₹{{ number_format($project->budget, 2) }}</td>
            </tr>

            <tr>
                <th>Deadline</th>
                <td>{{ $project->deadline }}</td>
            </tr>

            <tr>
                <th>Status</th>

                <td>

                    @switch($project->status)
                        @case('draft')
                            <span class="badge bg-secondary">Draft</span>
                        @break

                        @case('active')
                            <span class="badge bg-success">Active</span>
                        @break

                        @case('completed')
                            <span class="badge bg-primary">Completed</span>
                        @break

                        @case('cancelled')
                            <span class="badge bg-danger">Cancelled</span>
                        @break
                    @endswitch

                </td>

            </tr>

        </table>

        <hr>

        <h6>Description</h6>

        {!! nl2br(e($project->description)) !!}

    </div>

</div>
