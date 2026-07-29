<li class="header-notification">

    <div class="dropdown-primary dropdown">

        <div class="dropdown-toggle" data-bs-toggle="dropdown" id="notificationDropdownBtn">

            <i class="feather icon-bell"></i>

            <span class="badge bg-c-red" id="notificationCount">
                0
            </span>

        </div>

        <ul class="show-notification notification-view dropdown-menu">

            <li class="d-flex justify-content-between align-items-center">

                <h6 class="mb-0">

                    Notifications

                </h6>

                <a href="{{ route('seller.notifications.index') }}" class="text-primary">

                    View All

                </a>

            </li>

            <div id="notificationList">

                <li class="text-center py-4">

                    <i class="feather icon-bell f-30 text-muted"></i>

                    <p class="mt-2 mb-0">

                        No Notifications

                    </p>

                </li>

            </div>

        </ul>

    </div>

</li>
