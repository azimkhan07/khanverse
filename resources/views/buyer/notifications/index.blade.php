@extends('buyer.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1"> Notifications </h4>
                    <small class="text-muted">
                        Stay updated with your orders, projects and seller activities.
                    </small>
                </div>

                <button class="btn btn-primary" id="markAllReadBtn">
                    <i class="ti-check"></i>
                    Mark All Read
                </button>
            </div>
            <div class="card-body p-0">
                @forelse($notifications as $notification)
                    @php
                        $type = $notification->data['type'] ?? '';
                        $icon = 'ti-bell';
                        $bg = 'bg-primary';

                        switch ($type) {
                            case 'order':
                                $icon = 'ti-shopping-cart';
                                $bg = 'bg-success';
                                break;

                            case 'project':
                                $icon = 'ti-briefcase';
                                $bg = 'bg-primary';
                                break;

                            case 'chat':
                                $icon = 'ti-comments';
                                $bg = 'bg-info';
                                break;

                            case 'delivery':
                                $icon = 'ti-package';
                                $bg = 'bg-warning';
                                break;

                            case 'payment':
                                $icon = 'ti-wallet';
                                $bg = 'bg-success';
                                break;

                            case 'review':
                                $icon = 'ti-star';
                                $bg = 'bg-danger';
                                break;
                        }
                    @endphp

                    <a href="javascript:void(0)" class="notification-item text-decoration-none text-dark"
                        data-id="{{ $notification->id }}">
                        <div class="border-bottom p-3 {{ $notification->is_read ? '' : 'bg-light' }}">
                            <div class="d-flex">
                                <div class="rounded-circle {{ $bg }} d-flex justify-content-center align-items-center"
                                    style="width:55px;height:55px;">
                                    <i class="{{ $icon }} text-white"></i>
                                </div>

                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1"> {{ $notification->data['title'] ?? 'Notification' }} </h6>
                                        @if (is_null($notification->is_read))
                                            <span class="badge bg-danger"> New </span>
                                        @endif
                                    </div>

                                    <p class="mb-1 text-muted">
                                        {{ $notification->data['message'] ?? '' }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="ti-time"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-5">
                        <i class="ti-bell display-4 text-muted"></i>
                        <h5 class="mt-3"> No Notifications </h5>
                        <p class="text-muted mb-0">
                            You're all caught up.
                        </p>
                    </div>
                @endforelse
            </div>

            @if ($notifications->count())
                <div class="card-footer">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.notification-item', function() {
            let id = $(this).data('id');
            let url = $(this).data('url');
            $.post('/buyer/notifications/read/' + id, function() {
                if (url) {
                    window.location.href = url;
                } else {
                    location.reload();
                }
            });
        });

        $('#markAllReadBtn').click(function() {
            $.post('/buyer/notifications/read-all', function(res) {
                toastr.success('All notifications marked as read.');
                setTimeout(function() {
                    location.reload();
                }, 500);
            });
        });
    </script>
@endpush
