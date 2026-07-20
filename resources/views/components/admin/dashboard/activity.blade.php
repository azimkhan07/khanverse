<div class="card shadow border-0">

    <div class="card-header bg-white d-flex justify-content-between align-items-center">

        <h5 class="mb-0">

            <i class="fas fa-history text-primary"></i>

            Recent Activity

        </h5>

        <a href="#" class="text-decoration-none">

            View All

        </a>

    </div>

    <div class="card-body">

        <div class="timeline">

            @forelse($activities ?? [] as $activity)
                <div class="timeline-item d-flex mb-4">

                    <div class="timeline-icon">

                        @switch($activity['type'])
                            @case('order')
                                <span class="badge bg-success rounded-circle p-3">

                                    <i class="fas fa-shopping-cart"></i>

                                </span>
                            @break

                            @case('seller')
                                <span class="badge bg-primary rounded-circle p-3">

                                    <i class="fas fa-store"></i>

                                </span>
                            @break

                            @case('buyer')
                                <span class="badge bg-warning rounded-circle p-3">

                                    <i class="fas fa-user"></i>

                                </span>
                            @break

                            @case('project')
                                <span class="badge bg-info rounded-circle p-3">

                                    <i class="fas fa-project-diagram"></i>

                                </span>
                            @break

                            @default
                                <span class="badge bg-secondary rounded-circle p-3">

                                    <i class="fas fa-bell"></i>

                                </span>
                        @endswitch

                    </div>

                    <div class="ms-3 flex-grow-1">

                        <h6 class="mb-1">

                            {{ $activity['title'] }}

                        </h6>

                        <small class="text-muted">

                            {{ $activity['time'] }}

                        </small>

                    </div>

                </div>

                @empty

                    <div class="text-center py-5">

                        <i class="fas fa-history fa-3x text-muted mb-3"></i>

                        <p class="text-muted">

                            No recent activity found.

                        </p>

                    </div>
                @endforelse

            </div>

        </div>

    </div>

    <style>
        .timeline-item {

            position: relative;

            transition: .3s;

        }

        .timeline-item:hover {

            transform: translateX(8px);

        }

        .timeline-icon {

            min-width: 55px;

        }
    </style>
