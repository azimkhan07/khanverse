@extends('seller.layouts.app')

@section('title', 'My Profile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('seller/assets/css/profile.css') }}">
@endpush

@section('content')

    <div class="container-fluid profile-page">

        {{-- =========================================
        Profile Cover
    ========================================== --}}

        <div class="profile-cover mb-5">

            <div class="cover-content">

                <div class="row align-items-center">

                    <div class="col-lg-2 text-center">

                        <div class="position-relative d-inline-block">

                            <div class="profile-avatar">

                                @if (auth()->user()->profile_image)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile">
                                @else
                                    <img src="{{ asset('admin/assets/images/default-user.png') }}" alt="Profile">
                                @endif

                            </div>

                            <span class="online-indicator"></span>

                        </div>

                    </div>

                    <div class="col-lg-7">

                        <h2 class="profile-name">

                            {{ auth()->user()->name }}

                        </h2>

                        <div class="profile-role">

                            Seller Account

                        </div>

                        <div class="mt-2">

                            <span class="badge bg-success">

                                Verified Seller

                            </span>

                        </div>

                        <div class="profile-actions">

                            <button class="btn btn-light">

                                <i class="fa fa-user-edit me-2"></i>

                                Edit Profile

                            </button>

                            <button class="btn gradient-btn">

                                <i class="fa fa-camera me-2"></i>

                                Change Photo

                            </button>

                        </div>

                    </div>

                    <div class="col-lg-3 text-end">

                        <div class="glass-card p-4">

                            <div class="small text-white-50">

                                Current Time

                            </div>

                            <h3 id="liveClock" class="mb-0">

                            </h3>

                            <hr>

                            <div class="small text-dark-50">

                                Member Since

                            </div>

                            <div class="small text-dark-50">
                                Today
                            </div>

                            <h5 class="mb-0" id="liveDate"></h5>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        {{-- =========================================
        Stats
    ========================================== --}}

        <div class="row mb-4">

            <div class="col-lg-3 mb-4">

                <div class="profile-card stats-box">

                    <h2>

                        <span class="counter" data-target="24">0</span>

                    </h2>

                    <span>

                        Total Projects

                    </span>

                </div>

            </div>

            <div class="col-lg-3 mb-4">

                <div class="profile-card stats-box">

                    <h2>

                        <span class="counter" data-target="118">0</span>

                    </h2>

                    <span>

                        Completed Orders

                    </span>

                </div>

            </div>

            <div class="col-lg-3 mb-4">

                <div class="profile-card stats-box">

                    <h2>

                        ₹ <span class="counter" data-target="45800">0</span>

                    </h2>

                    <span>

                        Wallet Balance

                    </span>

                </div>

            </div>

            <div class="col-lg-3 mb-4">

                <div class="profile-card stats-box">

                    <h2>

                        <span class="counter" data-target="97">0</span>%

                    </h2>

                    <span>

                        Client Satisfaction

                    </span>

                </div>

            </div>

        </div>



        <div class="row">

            {{-- =========================================
            Left Sidebar
        ========================================== --}}

            <div class="col-lg-4">

                {{-- Profile Completion --}}

                <div class="glass-card completion-card mb-4">

                    <h5 class="mb-4">

                        Profile Completion

                    </h5>

                    <div class="progress-circle" data-progress="72">

                        <div class="inner">

                            0%

                        </div>

                    </div>

                    <p class="text-muted mt-4">

                        Complete your profile to attract more buyers.

                    </p>

                </div>



                {{-- Seller Information --}}

                <div class="profile-card p-4 mb-4">

                    <h5 class="mb-4">

                        Seller Information

                    </h5>

                    <ul class="profile-list">

                        <li>

                            <span>Email</span>

                            <strong>

                                {{ auth()->user()->email }}

                            </strong>

                        </li>

                        <li>

                            <span>Phone</span>

                            <strong>

                                {{ auth()->user()->phone ?? 'N/A' }}

                            </strong>

                        </li>

                        <li>

                            <span>Status</span>

                            <span class="badge bg-success">

                                Active

                            </span>

                        </li>

                        <li>

                            <span>Role</span>

                            <strong>

                                Seller

                            </strong>

                        </li>

                    </ul>

                </div>



                {{-- Skills --}}

                <div class="profile-card p-4">

                    <h5 class="mb-4">

                        Skills

                    </h5>

                    <span class="skill-item">

                        Laravel

                    </span>

                    <span class="skill-item">

                        PHP

                    </span>

                    <span class="skill-item">

                        Bootstrap

                    </span>

                    <span class="skill-item">

                        JavaScript

                    </span>

                    <span class="skill-item">

                        MySQL

                    </span>

                    <span class="skill-item">

                        API

                    </span>

                </div>

            </div>



            {{-- =========================================
            Right Section
        ========================================== --}}

            <div class="col-lg-8">

                {{-- =========================================
    Profile Tabs
========================================== --}}

                <div class="profile-card p-4">

                    <ul class="nav profile-tabs mb-4" id="profileTab" role="tablist">

                        <li class="nav-item">

                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overview">

                                <i class="fa fa-user me-2"></i>

                                Personal Info

                            </button>

                        </li>

                        <li class="nav-item">

                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#security">

                                <i class="fa fa-lock me-2"></i>

                                Security

                            </button>

                        </li>

                        <li class="nav-item">

                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#social">

                                <i class="fa fa-globe me-2"></i>

                                Social

                            </button>

                        </li>

                        <li class="nav-item">

                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#activity">

                                <i class="fa fa-history me-2"></i>

                                Activity

                            </button>

                        </li>

                    </ul>



                    <div class="tab-content">

                        {{-- =====================================
                            Personal Information
                        ====================================== --}}

                        <div class="tab-pane fade show active" id="overview">

                            <form action="{{ route('seller.profile.update') }}" method="POST" class="profile-form">

                                @csrf

                                <div class="row">

                                    <div class="col-md-6 mb-4">

                                        <label class="form-label">

                                            Full Name

                                        </label>

                                        <input type="text" name="name" value="{{ auth()->user()->name }}"
                                            class="form-control">

                                    </div>

                                    <div class="col-md-6 mb-4">

                                        <label class="form-label">

                                            Email Address

                                        </label>

                                        <input type="email" name="email" value="{{ auth()->user()->email }}"
                                            class="form-control">

                                    </div>

                                    <div class="col-md-6 mb-4">

                                        <label class="form-label">

                                            Phone Number

                                        </label>

                                        <input type="text" name="phone" value="{{ auth()->user()->phone }}"
                                            class="form-control">

                                    </div>

                                    <div class="col-md-6 mb-4">

                                        <label class="form-label">

                                            Username

                                        </label>

                                        <input type="text" value="{{ auth()->user()->username }}"
                                            class="form-control" readonly>

                                    </div>

                                    <div class="col-12 mb-4">

                                        <label class="form-label">

                                            About

                                        </label>

                                        <textarea class="form-control" rows="5" placeholder="Write something about yourself..."></textarea>

                                    </div>

                                    <div class="col-12">

                                        <button class="btn gradient-btn">

                                            <i class="fa fa-save me-2"></i>

                                            Save Profile

                                        </button>

                                    </div>

                                </div>

                            </form>

                        </div>



                        {{-- =====================================
                            Security
                        ====================================== --}}

                        <div class="tab-pane fade" id="security">

                            <form action="{{ route('seller.profile.password') }}" method="POST" class="profile-form">

                                @csrf

                                <div class="mb-4">

                                    <label class="form-label">

                                        Current Password

                                    </label>

                                    <input type="password" name="current_password" class="form-control">

                                </div>

                                <div class="mb-4">

                                    <label class="form-label">

                                        New Password

                                    </label>

                                    <input type="password" id="password" name="password" class="form-control">

                                    <small id="passwordStrength"></small>

                                </div>

                                <div class="mb-4">

                                    <label class="form-label">

                                        Confirm Password

                                    </label>

                                    <input type="password" name="password_confirmation" class="form-control">

                                </div>

                                <button class="btn gradient-btn">

                                    <i class="fa fa-lock me-2"></i>

                                    Update Password

                                </button>

                            </form>

                        </div>



                        {{-- =====================================
                                Social Links
                            ====================================== --}}

                        <div class="tab-pane fade" id="social">

                            <div class="social-card">

                                <div>

                                    <i class="fab fa-facebook text-primary"></i>

                                    Facebook

                                </div>

                                <input type="text" class="form-control w-50" placeholder="Facebook URL">

                            </div>

                            <div class="social-card">

                                <div>

                                    <i class="fab fa-linkedin text-info"></i>

                                    LinkedIn

                                </div>

                                <input type="text" class="form-control w-50" placeholder="LinkedIn URL">

                            </div>

                            <div class="social-card">

                                <div>

                                    <i class="fab fa-github"></i>

                                    Github

                                </div>

                                <input type="text" class="form-control w-50" placeholder="Github URL">

                            </div>

                            <button class="btn gradient-btn mt-3">

                                Save Social Links

                            </button>

                        </div>

                        {{-- =====================================
    Activity
===================================== --}}

                        <div class="tab-pane fade" id="activity">

                            <div class="timeline">

                                <div class="timeline-item">

                                    <span class="timeline-dot"></span>

                                    <div class="glass-card p-4 mb-4">

                                        <h6 class="timeline-title">

                                            Account Created

                                        </h6>

                                        <small class="timeline-date">

                                            {{ auth()->user()->created_at->format('d M Y h:i A') }}

                                        </small>

                                        <p class="mt-3 mb-0 text-muted">

                                            Your seller account was successfully created.

                                        </p>

                                    </div>

                                </div>

                                <div class="timeline-item">

                                    <span class="timeline-dot"></span>

                                    <div class="glass-card p-4 mb-4">

                                        <h6 class="timeline-title">

                                            First Project Completed

                                        </h6>

                                        <small class="timeline-date">

                                            Coming Soon

                                        </small>

                                        <p class="mt-3 mb-0 text-muted">

                                            This activity will automatically appear after completing your first project.

                                        </p>

                                    </div>

                                </div>

                                <div class="timeline-item">

                                    <span class="timeline-dot"></span>

                                    <div class="glass-card p-4">

                                        <h6 class="timeline-title">

                                            Wallet Updated

                                        </h6>

                                        <small class="timeline-date">

                                            Coming Soon

                                        </small>

                                        <p class="mt-3 mb-0 text-muted">

                                            Wallet transactions and earnings history will be displayed here.

                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                {{-- =====================================
    Bottom Widgets
===================================== --}}

                <div class="row mt-5">

                    <div class="col-lg-4 mb-4">

                        <div class="glass-card p-4 shadow-hover">

                            <h5 class="mb-4">

                                Achievement

                            </h5>

                            <div class="text-center">

                                <i class="fas fa-award fa-4x text-warning mb-3"></i>

                                <h4>New Seller</h4>

                                <p class="text-muted mb-0">

                                    Complete more projects to unlock new badges.

                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="col-lg-4 mb-4">

                        <div class="glass-card p-4 shadow-hover">

                            <h5 class="mb-4">

                                Wallet Overview

                            </h5>

                            <h2 class="text-gradient">

                                ₹0.00

                            </h2>

                            <p class="text-muted">

                                Available Balance

                            </p>

                            <button class="btn gradient-btn w-100">

                                View Wallet

                            </button>

                        </div>

                    </div>

                    <div class="col-lg-4 mb-4">

                        <div class="glass-card p-4 shadow-hover">

                            <h5 class="mb-4">

                                Rating

                            </h5>

                            <div class="text-center">

                                <h2 class="text-warning">

                                    ★★★★★

                                </h2>

                                <h3>

                                    5.0

                                </h3>

                                <p class="text-muted">

                                    Seller Rating

                                </p>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('seller/assets/js/profile.js') }}"></script>
@endpush
