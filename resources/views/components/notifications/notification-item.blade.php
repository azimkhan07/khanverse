@forelse($notifications as $notification)
    <li>
        <a href="{{ $notification->url }}" class="notification-link" data-id="{{ $notification->id }}">

            <h6 class="mb-1">
                {{ $notification->title }}
            </h6>

            <p class="mb-1">
                {{ $notification->message }}
            </p>

            <small class="text-muted">
                {{ $notification->created_at->diffForHumans() }}
            </small>

        </a>
    </li>

@empty

    <li class="text-center py-4">

        <i class="feather icon-bell f-30 text-muted"></i>

        <p class="mt-2 mb-0">
            No Notifications
        </p>

    </li>
@endforelse
