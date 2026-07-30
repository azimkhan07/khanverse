@extends('seller.layouts.app')

@section('title', 'Account Settings')

@section('content')

    <div class="page-body">

        <div class="page-header">

            <div class="page-header-title">

                <h4>

                    <i class="feather icon-settings"></i>

                    Account Settings

                </h4>

            </div>

        </div>

        <div class="row">

            <!-- Left Menu -->

            <div class="col-md-3">

                <div class="card">

                    <div class="list-group list-group-flush">

                        <a href="javascript:void(0)" class="list-group-item list-group-item-action setting-tab active"
                            data-target="password-section">

                            <i class="feather icon-lock me-2"></i>

                            Password

                        </a>

                        <a href="javascript:void(0)" class="list-group-item list-group-item-action setting-tab"
                            data-target="notification-section">

                            <i class="feather icon-bell me-2"></i>

                            Notifications

                        </a>

                        <a href="javascript:void(0)" class="list-group-item list-group-item-action setting-tab"
                            data-target="device-section">

                            <i class="feather icon-smartphone me-2"></i>

                            Login Devices

                        </a>

                        <a href="javascript:void(0)" class="list-group-item list-group-item-action setting-tab"
                            data-target="privacy-section">

                            <i class="feather icon-shield me-2"></i>

                            Privacy

                        </a>

                        <a href="javascript:void(0)" class="list-group-item list-group-item-action text-danger setting-tab"
                            data-target="delete-section">

                            <i class="feather icon-trash me-2"></i>

                            Delete Account

                        </a>

                    </div>

                </div>

            </div>

            <!-- Right Content -->

            <div class="col-md-9">

                <!-- Password -->
                <div id="password-section" class="setting-content">

                    <div class="card">

                        <div class="card-header">

                            <h5>

                                Change Password

                            </h5>

                        </div>

                        <div class="card-body">

                            <form id="passwordForm" method="POST" action="{{ route('seller.settings.password.update') }}">

                                @csrf

                                <div class="mb-3">

                                    <label class="form-label">

                                        Current Password

                                    </label>

                                    <input type="password" name="current_password" class="form-control" required>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">

                                        New Password

                                    </label>

                                    <input type="password" name="password" class="form-control" required>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">

                                        Confirm Password

                                    </label>

                                    <input type="password" name="password_confirmation" class="form-control" required>

                                </div>

                                <button class="btn btn-primary">

                                    <i class="feather icon-save"></i>

                                    Update Password

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

                <!-- Notifications -->
                <div id="notification-section" class="setting-content" style="display:none;">

                    <div class="card">

                        <div class="card-header">

                            <h5>

                                Notification Preferences

                            </h5>

                        </div>

                        <div class="card-body">

                            <form id="notificationForm" method="POST" action="{{ route('seller.settings.notifications.update') }}">

                                @csrf

                                <div class="form-check form-switch mb-4">

                                    <input class="form-check-input" type="checkbox" id="email_notification"
                                        name="email_notification" checked>

                                    <label class="form-check-label" for="email_notification">

                                        Email Notifications

                                    </label>

                                    <small class="text-muted d-block">

                                        Receive important updates through email.

                                    </small>

                                </div>

                                <div class="form-check form-switch mb-4">

                                    <input class="form-check-input" type="checkbox" id="sms_notification"
                                        name="sms_notification">

                                    <label class="form-check-label" for="sms_notification">

                                        SMS Notifications

                                    </label>

                                    <small class="text-muted d-block">

                                        Receive important updates via SMS.

                                    </small>

                                </div>

                                <div class="form-check form-switch mb-4">

                                    <input class="form-check-input" type="checkbox" id="push_notification"
                                        name="push_notification" checked>

                                    <label class="form-check-label" for="push_notification">

                                        Push Notifications

                                    </label>

                                    <small class="text-muted d-block">

                                        Receive browser push notifications.

                                    </small>

                                </div>

                                <button class="btn btn-primary">

                                    <i class="feather icon-save"></i>

                                    Save Preferences

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

                <!-- Login Devices -->
                <div id="device-section" class="setting-content" style="display:none;">
                    <div class="card">
                        <div class="card-header">
                            <h5> Login Devices </h5>
                        </div>
                        <div class="card-body">
                            @if ($devices->count())
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Device</th>
                                                <th>IP Address</th>
                                                <th>Last Login</th>
                                                <th width="120">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($devices as $device)
                                                <tr>
                                                    <td>{{ $device->device_name ?? 'Unknown Device' }}</td>
                                                    <td>{{ $device->ip_address }}</td>
                                                    <td>{{ $device->updated_at->diffForHumans() }}</td>
                                                    <td>

                                                        <form method="POST" class="removeDeviceForm" action="{{ route('seller.settings.devices.remove', $device->id) }}" onsubmit="return confirm('Remove this device?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-danger btn-sm"><i class="feather icon-trash"></i> Remove </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    No Login Devices Found.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Privacy -->
                <div id="privacy-section" class="setting-content" style="display:none;">
                    <div class="card">
                        <div class="card-header">
                            <h5>  Privacy Settings </h5>
                        </div>

                        <div class="card-body">
                            <form method="POST" id="privacyForm" action="{{ route('seller.settings.privacy.update') }}">
                                @csrf
                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" id="public_profile" name="public_profile" checked>
                                    <label class="form-check-label" for="public_profile"> Public Profile </label>

                                    <small class="text-muted d-block">
                                        Allow other users to view your seller profile.
                                    </small>
                                </div>

                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" id="online_status" name="online_status" checked>
                                    <label class="form-check-label" for="online_status"> Show Online Status </label>

                                    <small class="text-muted d-block">
                                        Display your online/offline status in chat.
                                    </small>
                                </div>

                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" id="search_engine" name="search_engine">
                                    <label class="form-check-label" for="search_engine"> Search Engine Visibility </label>

                                    <small class="text-muted d-block">
                                        Allow search engines to index your public profile.
                                    </small>
                                </div>

                                <button class="btn btn-primary"> <i class="feather icon-save"></i> Save Privacy Settings </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Account -->
                <div id="delete-section" class="setting-content" style="display:none;">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"> Delete Account </h5>
                        </div>

                        <div class="card-body">
                            <div class="alert alert-warning">
                                <strong>Warning!</strong>
                                Deleting your account is permanent.
                                This feature is currently unavailable because your
                                account may contain active projects, orders, wallet balance,
                                or other important data.
                            </div>

                            <button class="btn btn-danger" disabled> <i class="feather icon-trash"></i> Delete My Account </button>

                            <p class="text-muted mt-3 mb-0">
                                This feature will be available in a future update.
                            </p>
                        </div>
                    </div>
                </div>
                <!-- End Right Column -->
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Body -->
    </div>
@endsection
@push('scripts')
    <script>
        $(function() {
            $(".setting-content").hide();
            $("#password-section").show();
            $(".setting-tab").removeClass("active");
            $('[data-target="password-section"]').addClass("active");
        });

        $(document).on("click", ".setting-tab", function(e) {
            e.preventDefault();
            let target = $(this).data("target");
            $(".setting-tab").removeClass("active");
            $(this).addClass("active");
            $(".setting-content").hide();
            $("#" + target).fadeIn(200);
        });

        // ================= PASSWORD UPDATE =================

        $("#passwordForm").submit(function(e) {

            e.preventDefault();
            let form = $(this);

            $.ajax({
                url: form.attr("action"),
                type: "POST",
                data: form.serialize(),
                beforeSend: function() {
                    form.find("button[type=submit]").prop("disabled", true);
                },
                success: function(response) {
                    toastr.success(response.message);
                    form.trigger("reset");
                },
                error: function(xhr) {
                    if (xhr.status == 422) {
                        if (xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message);
                        }

                        if (xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        }
                    } else {
                        toastr.error("Something went wrong.");
                    }
                },

                complete: function() {
                    form.find("button[type=submit]").prop("disabled", false);
                }
            });
        });

        // ================= NOTIFICATION =================

        $("#notificationForm").submit(function(e) {

            e.preventDefault();
            let form = $(this);

            $.ajax({

                url: form.attr("action"),
                type: "POST",
                data: form.serialize(),

                beforeSend: function() {
                    form.find("button[type=submit]").prop("disabled", true);
                },
                success: function(response) {
                    toastr.success(response.message);
                },
                error: function() {
                    toastr.error("Something went wrong.");
                },
                complete: function() {
                    form.find("button[type=submit]").prop("disabled", false);
                }
            });
        });

        // ================= PRIVACY =================

        $("#privacyForm").submit(function(e) {

            e.preventDefault();
            let form = $(this);

            $.ajax({
                url: form.attr("action"),
                type: "POST",
                data: form.serialize(),
                beforeSend: function() {
                    form.find("button[type=submit]").prop("disabled", true);
                },
                success: function(response) {
                    toastr.success(response.message);
                },
                error: function() {
                    toastr.error("Something went wrong.");
                },
                complete: function() {
                    form.find("button[type=submit]").prop("disabled", false);
                }
            });
        });

        // ================= REMOVE DEVICE =================

        $(document).on("submit", ".removeDeviceForm", function(e) {
            e.preventDefault();
            let form = $(this);
            if (!confirm("Remove this device?")) {
                return;
            }

            $.ajax({
                url: form.attr("action"),
                type: "POST",
                data: form.serialize(),
                success: function(response) {
                    toastr.success(response.message);
                    form.closest("tr").fadeOut(300, function() {
                        $(this).remove();
                    });
                },
                error: function() {
                    toastr.error("Something went wrong.");
                }
            });
        });
    </script>
@endpush
<style>
    .list-group-item.setting-tab {
        color: #333;
        font-weight: 500;
        transition: .3s;
        border: 0;
        border-left: 4px solid transparent;
        background: #fff;
    }

    .list-group-item.setting-tab:hover {
        background: #f8f9fa;
        color: #0d6efd;
    }

    .list-group-item.setting-tab.active {
        background: #0d6efd !important;
        color: #fff !important;
        border-left: 4px solid #084298;
    }

    .list-group-item.setting-tab.active i {
        color: #fff !important;
    }

    .list-group-item.setting-tab.active:hover {
        background: #0d6efd !important;
        color: #fff !important;
    }
</style>
