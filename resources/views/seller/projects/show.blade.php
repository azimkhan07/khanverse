@extends('seller.layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Header -->

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="mb-1">

                    {{ $project->title }}

                </h3>

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

                        <strong>

                            Project Overview

                        </strong>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="text-muted">

                                    Buyer

                                </label>

                                <h6>

                                    {{ optional($project->buyer)->full_name ?? 'N/A' }}

                                </h6>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="text-muted">

                                    Budget

                                </label>

                                <h6>

                                    ${{ number_format($project->budget, 2) }}

                                </h6>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="text-muted">

                                    Deadline

                                </label>

                                <h6>

                                    {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}

                                </h6>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="text-muted">

                                    Status

                                </label>

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

                <div class="card shadow-sm border-0 mb-4">

                    <div class="card-header">

                        <strong>

                            Project Status

                        </strong>

                    </div>

                    <div class="card-body">

                        <form action="{{ route('seller.projects.status', $project->id) }}" method="POST">

                            @csrf

                            <div class="mb-3">

                                <label class="form-label">

                                    Current Status

                                </label>

                                <select name="status" class="form-select">

                                    <option value="open" {{ $project->status == 'open' ? 'selected' : '' }}>
                                        Open
                                    </option>

                                    <option value="in_progress" {{ $project->status == 'in_progress' ? 'selected' : '' }}>
                                        In Progress
                                    </option>

                                    <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>
                                        Completed
                                    </option>

                                    <option value="cancelled" {{ $project->status == 'cancelled' ? 'selected' : '' }}>
                                        Cancelled
                                    </option>

                                </select>

                            </div>

                            <button class="btn btn-primary">

                                <i class="ti-check"></i>

                                Update Status

                            </button>

                        </form>

                    </div>

                </div>

                <!-- Quick Actions -->

                <div class="card shadow-sm border-0 mb-4">

                    <div class="card-header">

                        <strong>

                            Quick Actions

                        </strong>

                    </div>

                    <div class="card-body d-grid gap-2">

                        <a href="{{ route('seller.projects.attachments', $project->id) }}" class="btn btn-primary">

                            <i class="ti-folder mr-2"></i>

                            Project Files

                        </a>

                        <button class="btn btn-success" disabled>

                            <i class="ti-comments mr-2"></i>

                            Open Chat (Coming Soon)

                        </button>

                        <button class="btn btn-warning" disabled>

                            <i class="ti-reload mr-2"></i>

                            Change Status

                        </button>

                    </div>

                </div>



                <!-- Project Summary -->

                <div class="card shadow-sm border-0 mb-4">

                    <div class="card-header">

                        <strong>

                            Project Summary

                        </strong>

                    </div>

                    <div class="card-body">

                        <table class="table table-borderless mb-0">

                            <tr>

                                <td>

                                    Buyer

                                </td>

                                <td class="text-end">

                                    {{ optional($project->buyer)->full_name ?? 'N/A' }}

                                </td>

                            </tr>

                            <tr>

                                <td>

                                    Seller

                                </td>

                                <td class="text-end">

                                    {{ optional($project->seller)->full_name ?? 'N/A' }}

                                </td>

                            </tr>

                            <tr>

                                <td>

                                    Budget

                                </td>

                                <td class="text-end">

                                    ${{ number_format($project->budget, 2) }}

                                </td>

                            </tr>

                            <tr>

                                <td>

                                    Deadline

                                </td>

                                <td class="text-end">

                                    {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}

                                </td>

                            </tr>

                            <tr>

                                <td>

                                    Created

                                </td>

                                <td class="text-end">

                                    {{ $project->created_at->format('d M Y') }}

                                </td>

                            </tr>

                        </table>

                    </div>

                </div>

                <div class="card shadow-sm border-0 mt-4">

                    <div class="card-header">

                        <strong>

                            Project Activity

                        </strong>

                    </div>

                    <div class="card-body">

                        <ul class="timeline list-unstyled">

                            <li class="mb-4">

                                <strong>

                                    Project Created

                                </strong>

                                <br>

                                <small class="text-muted">

                                    {{ $project->created_at->format('d M Y h:i A') }}

                                </small>

                            </li>

                            @foreach ($project->attachments as $attachment)
                                <li class="mb-4">

                                    <strong>

                                        {{ ucfirst($attachment->uploaded_by) }}

                                        uploaded

                                        {{ $attachment->file_name }}

                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        {{ $attachment->created_at->format('d M Y h:i A') }}

                                    </small>

                                </li>
                            @endforeach

                            <li>

                                <strong>

                                    Current Status :

                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}

                                </strong>

                            </li>

                        </ul>

                    </div>

                </div>

                <!-- Progress -->

                <div class="card shadow-sm border-0">

                    <div class="card-header">

                        <strong>

                            Progress

                        </strong>

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

        <!-- =========================
                            PROJECT WORKSPACE
                        ========================== -->

        <div class="card shadow-sm border-0 mt-4">

            <div class="card-header">

                <ul class="nav nav-tabs card-header-tabs" id="projectTabs" role="tablist">

                    <li class="nav-item">

                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#chatTab">

                            <i class="ti-comments"></i>

                            Chat

                        </button>

                    </li>

                    <li class="nav-item">

                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#filesTab">

                            <i class="ti-folder"></i>

                            Files

                        </button>

                    </li>

                    <li class="nav-item">

                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#activityTab">

                            <i class="ti-time"></i>

                            Activity

                        </button>

                    </li>

                </ul>

            </div>


            <div class="card-body">

                <div class="tab-content">

                    <!-- ================= CHAT ================= -->

                    <div class="tab-pane fade show active" id="chatTab">

                        <div class="text-center py-5">

                            <i class="ti-comments display-4 text-primary"></i>

                            <h5 class="mt-3">

                                Project Chat

                            </h5>

                            <p class="text-muted">

                                Every project has its own private chat.

                                Buyer and Seller can communicate only inside this project.

                            </p>

                            <button class="btn btn-primary" disabled>

                                Chat Module Coming Soon

                            </button>

                        </div>

                    </div>



                    <!-- ================= FILES ================= -->

                    <div class="tab-pane fade" id="filesTab">

                        <div class="text-center py-5">

                            <i class="ti-folder display-4 text-warning"></i>

                            <h5 class="mt-3">

                                Project Files

                            </h5>

                            <p class="text-muted">

                                Upload contracts, source code, documents and final delivery files.

                            </p>

                            <a href="{{ route('seller.projects.attachments', $project->id) }}" class="btn btn-warning">

                                Open File Manager

                            </a>

                        </div>

                    </div>



                    <!-- ================= ACTIVITY ================= -->

                    <div class="tab-pane fade" id="activityTab">

                        <ul class="list-group list-group-flush">

                            <li class="list-group-item">

                                <strong>

                                    Project Created

                                </strong>

                                <br>

                                <small class="text-muted">

                                    {{ $project->created_at->format('d M Y h:i A') }}

                                </small>

                            </li>

                            <li class="list-group-item">

                                <strong>

                                    Seller Assigned

                                </strong>

                                <br>

                                <small class="text-muted">

                                    {{ optional($project->seller)->full_name }}

                                </small>

                            </li>

                            <li class="list-group-item">

                                <strong>

                                    Current Status

                                </strong>

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
@endsection
