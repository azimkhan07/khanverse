@switch($status)
    @case('pending')
        <span class="badge bg-warning">

            Pending

        </span>
    @break

    @case('accepted')
        <span class="badge bg-info">

            Accepted

        </span>
    @break

    @case('in_progress')
        <span class="badge bg-primary">

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

            {{ ucfirst($status) }}

        </span>
@endswitch
