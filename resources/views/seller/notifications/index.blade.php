@extends('seller.layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <div>

                    <h4 class="mb-1">

                        Notifications

                    </h4>

                    <small class="text-muted">

                        Stay updated with your orders, projects and messages.

                    </small>

                </div>

                <button class="btn btn-primary" id="markAllReadBtn">

                    <i class="feather icon-check-circle"></i>

                    Mark All Read

                </button>

            </div>

            <div class="card-body p-0">

                @forelse($notifications as $notification)
                    @php

                        $icon = 'icon-bell';
                        $bg = 'bg-primary';

                        switch ($notification->type) {
                            case 'order':
                                $icon = 'icon-shopping-cart';
                                $bg = 'bg-success';
                                break;

                            case 'project':
                                $icon = 'icon-folder';
                                $bg = 'bg-primary';
                                break;

                            case 'chat':
                                $icon = 'icon-message-circle';
                                $bg = 'bg-info';
                                break;

                            case 'delivery':
                                $icon = 'icon-package';
                                $bg = 'bg-warning';
                                break;

                            case 'payment':
                                $icon = 'icon-dollar-sign';
                                $bg = 'bg-success';
                                break;

                            case 'review':
                                $icon = 'icon-star';
                                $bg = 'bg-danger';
                                break;

                            case 'revision':
                                $icon = 'icon-refresh-cw';
                                $bg = 'bg-warning';
                                break;

                            case 'dispute':
                                $icon = 'icon-alert-triangle';
                                $bg = 'bg-danger';
                                break;
                        }

                    @endphp

                    <a href="{{ route('seller.notifications.read', $notification->id) }}"
                        class="text-decoration-none text-dark">

                        <div class="border-bottom p-3 {{ $notification->is_read ? '' : 'bg-light' }}">

                            <div class="d-flex">

                                <div class="rounded-circle {{ $bg }} d-flex justify-content-center align-items-center"
                                    style="width:55px;height:55px;">

                                    <i class="feather {{ $icon }} text-white"></i>

                                </div>

                                <div class="flex-grow-1 ms-3">

                                    <div class="d-flex justify-content-between">

                                        <h6 class="mb-1">

                                            {{ $notification->title }}

                                        </h6>

                                        @if (!$notification->is_read)
                                            <span class="badge bg-danger">

                                                New

                                            </span>
                                        @endif

                                    </div>

                                    <p class="mb-1 text-muted">

                                        {{ $notification->message }}

                                    </p>

                                    <small class="text-muted">

                                        <i class="feather icon-clock"></i>

                                        {{ $notification->created_at->diffForHumans() }}

                                    </small>

                                </div>

                            </div>

                        </div>

                    </a>

                @empty

                    <div class="text-center py-5">

                        <i class="feather icon-bell f-50 text-muted"></i>

                        <h5 class="mt-3">

                            No Notifications Found

                        </h5>

                        <p class="text-muted">

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
