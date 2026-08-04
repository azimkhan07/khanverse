@extends('seller.layouts.app')
<link rel="stylesheet" href="{{ asset('seller/assets/css/chat.css') }}">
@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1"> {{ $project->title }} </h3>
                <small class="text-muted">
                    Project #{{ $project->id }}
                </small>
            </div>

            <div>
                <a href="{{ route('seller.projects.index') }}" class="btn btn-secondary">
                    <i class="ti-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="row">
            <!-- LEFT -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header">
                        <strong> Project Overview </strong>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted"> Buyer </label>
                                <h6> {{ optional($project->buyer)->full_name ?? 'N/A' }} </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="text-muted"> Budget </label>
                                <h6> ${{ number_format($project->budget, 2) }} </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="text-muted"> Deadline </label>
                                <h6> {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}
                                </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="text-muted"> Status </label>
                                <h6>
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
                                    @endswitch
                                </h6>
                            </div>
                        </div>
                        <hr>

                        <h5>
                            Description
                        </h5>

                        <p class="text-muted mb-0">
                            {!! nl2br(e($project->description)) !!}
                        </p>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDEBAR -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card shadow-sm border-0 mb-4">

                    <div class="card-header">
                        <strong>Quick Actions</strong>
                    </div>

                    <div class="card-body d-grid gap-2">
                        <button class="btn btn-primary" disabled>
                            <i class="ti-package mr-2"></i>
                            View Deliveries
                        </button>
                        <a class="btn btn-info" href="{{ route('buyer.projects.attachments', $project->id) }}"
                            class="btn btn-primary">
                            <i class="ti-download mr-2"></i>
                            Download Files
                        </a>
                        <button class="btn btn-success" disabled>
                            <i class="ti-server mr-2"></i>
                            Hosting Details
                        </button>
                        <a href="javascript:void(0)" id="openChat" data-project="{{ $project->id }}"
                            class="btn btn-primary">
                            <i class="ti-comments"></i>

                            Chat with Seller
                        </a>
                    </div>
                </div>

                <!-- Project Summary -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header">
                        <strong> Project Summary </strong>
                    </div>

                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td> Buyer </td>
                                <td class="text-end"> {{ optional($project->buyer)->full_name ?? 'N/A' }} </td>
                            </tr>
                            <tr>
                                <td> Seller </td>
                                <td class="text-end"> {{ optional($project->seller)->full_name ?? 'N/A' }} </td>
                            </tr>
                            <tr>
                                <td> Budget </td>
                                <td class="text-end"> ${{ number_format($project->budget, 2) }} </td>
                            </tr>
                            <tr>
                                <td> Deadline </td>
                                <td class="text-end">
                                    {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td> Created </td>
                                <td class="text-end"> {{ $project->created_at->format('d M Y') }} </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header">
                        <strong> Project Activity </strong>
                    </div>

                    <div class="card-body">
                        <ul class="timeline list-unstyled">
                            <li class="mb-4">
                                <strong> Project Created </strong>
                                <br>
                                <small class="text-muted"> {{ $project->created_at->format('d M Y h:i A') }} </small>
                            </li>
                            @foreach ($project->attachments as $attachment)
                                <li class="mb-4">
                                    <strong>
                                        {{ ucfirst($attachment->uploaded_by) }}
                                        uploaded
                                        {{ $attachment->file_name }}

                                    </strong>
                                    <br>
                                    <small class="text-muted"> {{ $attachment->created_at->format('d M Y h:i A') }}
                                    </small>
                                </li>
                            @endforeach
                            <li>
                                <strong> Current Status : {{ ucfirst(str_replace('_', ' ', $project->status)) }} </strong>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Progress -->
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <strong> Progress </strong>
                    </div>
                    <div class="card-body">
                        @php
                            $progress = match ($project->status) {
                                'open' => 10,
                                'in_progress' => 60,
                                'completed' => 100,
                                'cancelled' => 0,
                                default => 0,
                            };
                        @endphp
                        <div class="progress mb-2" style="height:10px;">
                            <div class="progress-bar" style="width:{{ $progress }}%">
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ $progress }}% Completed
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================= PROJECT WORKSPACE  ========================== -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#deliveriesTab"><i
                                class="ti-package"></i> Deliveries </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#filesTab"><i class="ti-folder"></i>
                            Files </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#activityTab"><i class="ti-time"></i>
                            Activity </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">

                    <!-- ================= DELIVERIES ================= -->
                    <div class="tab-pane fade show active" id="deliveriesTab">
                        <div class="text-center py-5">
                            <i class="ti-package display-4 text-primary"></i>
                            <h5 class="mt-3"> Project Deliveries </h5>
                            <p class="text-muted">
                                Seller delivery reports, milestones and
                                submitted documents will appear here.
                            </p>
                            <button class="btn btn-primary" disabled> No Deliveries Yet </button>
                        </div>
                    </div>

                    <!-- ================= FILES ================= -->
                    <div class="tab-pane fade" id="filesTab">
                        <div class="text-center py-5">
                            <i class="ti-folder display-4 text-warning"></i>
                            <h5 class="mt-3"> Project Files </h5>
                            <p class="text-muted">
                                Reports, documentation and shared
                                attachments will be available here.
                            </p>

                            <button class="btn btn-warning" disabled> No Files Available </button>
                        </div>
                    </div>

                    <!-- ================= ACTIVITY ================= -->
                    <div class="tab-pane fade" id="activityTab">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong> Project Created </strong>
                                <br>
                                <small class="text-muted">
                                    {{ $project->created_at->format('d M Y h:i A') }}
                                </small>
                            </li>

                            <li class="list-group-item">
                                <strong> Seller Assigned </strong>
                                <br>
                                <small class="text-muted">
                                    {{ optional($project->seller)->full_name ?? 'N/A' }}
                                </small>
                            </li>

                            <li class="list-group-item">
                                <strong> Current Status </strong>
                                <br>
                                <span class="badge bg-info">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('buyer.projects.partials.chat-popup')
@endsection
@push('scripts')
    <script>
        let chatMinimized = false;
        $("#minimizeChat").on("click", function() {
            if (!chatMinimized) {
                $("#chatContent").slideUp(200);
                $("#chatPopup").addClass("minimized");
                $("#minimizeChat i").removeClass("icon-minus").addClass("icon-plus");
                chatMinimized = true;
            } else {
                $("#chatContent").slideDown(200);
                $("#chatPopup").removeClass("minimized");
                $("#minimizeChat i").removeClass("icon-plus").addClass("icon-minus");
                chatMinimized = false;
            }
        });

        $("#closeChat").on("click", function() {
            $("#chatPopup").css("right", "-450px");
        });

        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $("#openChat").click(function() {
                let project = $(this).data("project");
                $.ajax({
                    url: "{{ route('buyer.chat.open') }}",
                    type: "POST",
                    data: {
                        project_id: project
                    },
                    success: function(response) {
                        $("#chatPopup").css("right", "20px");
                        $("#conversationId").val(response.conversation_id);
                        $("#chatUserName").text(response.seller.name);
                        loadMessages(response.conversation_id);
                        markAsSeen(response.conversation_id);
                    }
                });
            });
        });

        function loadMessages(id) {

            $.get("/buyer/chat/messages/" + id, function(response) {

                let html = "";

                let currentUser = {{ auth()->id() }};

                $.each(response.messages, function(index, message) {

                    let mine = message.sender_id == currentUser;
                    let senderName = message.sender.username;
                    let profileImage = "/admin/assets/images/avatar-4.jpg";

                    if (message.sender.role == "buyer") {
                        if (message.sender.buyer) {
                            senderName = message.sender.buyer.full_name;
                            if (message.sender.buyer.profile_image) {
                                profileImage = "/storage/" + message.sender.buyer.profile_image;
                            }
                        }
                    } else {
                        if (message.sender.seller) {
                            senderName = message.sender.seller.full_name;
                            if (message.sender.seller.profile_image) {
                                profileImage = "/storage/" + message.sender.seller.profile_image;
                            }
                        }
                    }

                    if (mine) {

                        html += `

                            <div class="d-flex justify-content-end mb-3">

                                <div>

                                    <div class="chat-bubble chat-me">

                                        ${message.message}

                                    </div>

                                    <small class="text-muted d-block text-end">

                                        ${message.chat_time}
                                    </small>

                                </div>

                            </div>`;

                    } else {

                        html += `

                        <div class="d-flex align-items-end mb-3">

                            <img src="${profileImage}"

                                class="rounded-circle me-2"

                                style="width:38px;height:38px;object-fit:cover;">

                            <div>

                                <div class="small fw-bold text-muted mb-1">

                                    ${senderName}

                                </div>

                                <div class="chat-bubble chat-other">
                                    ${message.message}
                                </div>

                                <small class="text-muted">
                                    ${message.chat_time}
                                </small>

                            </div>

                        </div>`;
                    }
                });
                $("#chatBody").html(html);
                $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
            });
        }

        $("#sendMessage").click(function() {
            let formData = new FormData();
            formData.append(
                "conversation_id",
                $("#conversationId").val()
            );
            formData.append(
                "message",
                $("#chatMessage").val()
            );
            if ($("#chatAttachment")[0].files.length) {
                formData.append(
                    "attachment",
                    $("#chatAttachment")[0].files[0]
                );
            }

            $.ajax({
                url: "{{ route('buyer.chat.send') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,

                success: function() {
                    $("#chatMessage").val('');
                    $("#chatAttachment").val('');
                    loadMessages($("#conversationId").val());
                }
            });
        });

        function markAsSeen(id) {
            $.post("{{ url('buyer/chat/seen') }}/" + id);
        }
    </script>
@endpush
<style>
    .chat-bubble {
        display: inline-block;
        padding: 10px 16px;
        border-radius: 20px;
        max-width: 320px;
        word-break: break-word;
    }
    .chat-me {
        background: #4f46e5;
        color: #fff;
        border-bottom-right-radius: 6px;
    }
    .chat-other {
        background: #f3f4f6;
        color: #222;
        border-bottom-left-radius: 6px;
    }
</style>
