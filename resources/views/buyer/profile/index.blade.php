@extends('seller.layouts.app')

@section('title', 'My Profile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('seller/assets/css/profile.css') }}">
    <style>
        /*=========================================================
      Seller Profile Design System
    =========================================================*/

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

        :root {

            --primary: #4F46E5;
            --primary2: #6366F1;

            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;

            --text: #111827;
            --text-light: #6B7280;

            --bg: #F4F7FC;

            --card: #ffffff;

            --border: #E8ECF4;

            --radius: 22px;

            --transition: .35s;

            --shadow:

                0 15px 45px rgba(15, 23, 42, .08);

        }

        /*====================================*/

        * {

            margin: 0;

            padding: 0;

            box-sizing: border-box;

        }

        /*====================================*/

        html {

            scroll-behavior: smooth;

        }

        /*====================================*/

        body {

            font-family: 'Poppins', sans-serif;

            background:

                linear-gradient(180deg,

                    #EEF4FF,

                    #F6F9FC,

                    #FFFFFF);

            color: var(--text);

        }

        /*====================================*/

        .profile-page {

            padding: 35px;

        }

        /*====================================*/

        a {

            text-decoration: none;

        }

        button {

            transition: var(--transition);

        }

        /*====================================*/

        .glass {

            background:

                rgba(255, 255, 255, .75);

            backdrop-filter:

                blur(18px);

            border:

                1px solid rgba(255, 255, 255, .45);

        }

        /*====================================*/

        .card-ui {

            background: var(--card);

            border-radius: 24px;

            border: 1px solid var(--border);

            box-shadow: var(--shadow);

            transition: .35s;

        }

        .card-ui:hover {

            transform: translateY(-6px);

        }

        /*====================================*/

        .section-title {

            font-size: 24px;

            font-weight: 700;

            margin-bottom: 20px;

        }

        /*====================================*/

        .subtitle {

            color: var(--text-light);

        }

        /*====================================*/

        .gradient-text {

            background:

                linear-gradient(135deg,

                    #4F46E5,

                    #06B6D4);

            -webkit-background-clip: text;

            -webkit-text-fill-color: transparent;

        }

        /*====================================*/

        .gradient-btn {

            border: none;

            color: #fff;

            border-radius: 50px;

            padding: 13px 28px;

            font-weight: 600;

            background:

                linear-gradient(135deg,

                    #4F46E5,

                    #06B6D4);

            box-shadow:

                0 10px 30px rgba(79, 70, 229, .30);

        }

        .gradient-btn:hover {

            transform:

                translateY(-3px);

        }

        /*====================================*/

        .light-btn {

            background: #fff;

            border-radius: 50px;

            border: none;

            padding: 13px 28px;

            font-weight: 600;

            box-shadow:

                0 8px 25px rgba(0, 0, 0, .06);

        }

        /*====================================*/

        .badge-ui {

            padding: 8px 18px;

            border-radius: 30px;

            font-size: 13px;

            font-weight: 600;

        }

        /*====================================*/

        .badge-success {

            color: #fff;

            background: #10B981;

        }

        .badge-primary {

            color: #fff;

            background: #4F46E5;

        }

        /*====================================*/

        .fade-up {

            animation: fadeUp .7s ease;

        }

        @keyframes fadeUp {

            from {

                opacity: 0;

                transform: translateY(35px);

            }

            to {

                opacity: 1;

                transform: translateY(0);

            }

        }

        /*====================================*/

        .float {

            animation: float 5s ease-in-out infinite;

        }

        @keyframes float {

            0% {

                transform: translateY(0);

            }

            50% {

                transform: translateY(-8px);

            }

            100% {

                transform: translateY(0);

            }

        }

        /*=========================================================
     HERO SECTION
    =========================================================*/

        .profile-cover {

            position: relative;

            overflow: hidden;

            border-radius: 32px;

            padding: 45px;

            min-height: 330px;

            background:

                linear-gradient(135deg,
                    #4338CA,
                    #4F46E5,
                    #06B6D4);

            box-shadow:
                0 30px 60px rgba(79, 70, 229, .25);

        }

        /*================================*/

        .profile-cover::before {

            content: "";

            position: absolute;

            width: 420px;

            height: 420px;

            right: -150px;

            top: -150px;

            border-radius: 50%;

            background:

                rgba(255, 255, 255, .08);

        }

        /*================================*/

        .profile-cover::after {

            content: "";

            position: absolute;

            width: 260px;

            height: 260px;

            left: -80px;

            bottom: -80px;

            border-radius: 50%;

            background:

                rgba(255, 255, 255, .08);

        }

        /*================================*/

        .cover-content {

            position: relative;

            z-index: 10;

        }

        /*================================*/

        .profile-avatar {

            width: 170px;

            height: 170px;

            border-radius: 50%;

            overflow: hidden;

            border: 7px solid rgba(255, 255, 255, .25);

            box-shadow:

                0 20px 45px rgba(0, 0, 0, .18);

            transition: .35s;

        }

        .profile-avatar:hover {

            transform: scale(1.05);

        }

        /*================================*/

        .profile-avatar img {

            width: 100%;

            height: 100%;

            object-fit: cover;

        }

        /*================================*/

        .online-indicator {

            position: absolute;

            width: 22px;

            height: 22px;

            border-radius: 50%;

            right: 10px;

            bottom: 15px;

            background: #22C55E;

            border: 4px solid #fff;

            animation: pulse 2s infinite;

        }

        @keyframes pulse {

            0% {

                box-shadow: 0 0 0 0 rgba(34, 197, 94, .6);

            }

            70% {

                box-shadow: 0 0 0 15px rgba(34, 197, 94, 0);

            }

            100% {

                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);

            }

        }

        /*================================*/

        .profile-name {

            color: #fff;

            font-size: 38px;

            font-weight: 700;

            margin-bottom: 10px;

        }

        /*================================*/

        .profile-role {

            color: rgba(255, 255, 255, .85);

            font-size: 17px;

        }

        /*================================*/

        .profile-meta {

            display: flex;

            flex-wrap: wrap;

            gap: 15px;

            margin-top: 20px;

        }

        .profile-meta span {

            color: #fff;

            font-size: 14px;

        }

        /*================================*/

        .profile-actions {

            margin-top: 35px;

        }

        .profile-actions .btn {

            margin-right: 12px;

        }

        /*================================*/

        .hero-info-card {

            background:

                rgba(255, 255, 255, .15);

            backdrop-filter: blur(15px);

            border-radius: 25px;

            padding: 28px;

            color: #fff;

            text-align: center;

            border: 1px solid rgba(255, 255, 255, .18);

        }

        .hero-info-card h2 {

            font-size: 38px;

            font-weight: 700;

        }

        .hero-info-card hr {

            border-color: rgba(255, 255, 255, .2);

        }

        .hero-info-card small {

            opacity: .8;

        }

        /*=========================================================
        STATS CARDS
    =========================================================*/

        .stats-section {

            margin-top: -60px;

            position: relative;

            z-index: 20;

        }

        .stat-card {

            position: relative;

            overflow: hidden;

            background: #fff;

            border-radius: 24px;

            padding: 28px;

            border: 1px solid #edf2f7;

            box-shadow: 0 15px 40px rgba(15, 23, 42, .08);

            transition: .35s;

        }

        .stat-card:hover {

            transform: translateY(-8px);

            box-shadow: 0 25px 55px rgba(79, 70, 229, .18);

        }

        /*--------------------------------*/

        .stat-card::before {

            content: "";

            position: absolute;

            top: -70px;

            right: -70px;

            width: 150px;

            height: 150px;

            border-radius: 50%;

            background: rgba(79, 70, 229, .06);

        }

        /*--------------------------------*/

        .stat-icon {

            width: 65px;

            height: 65px;

            border-radius: 18px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 28px;

            color: #fff;

            margin-bottom: 22px;

        }

        /*--------------------------------*/

        .bg-project {

            background: linear-gradient(135deg, #4F46E5, #6366F1);

        }

        .bg-order {

            background: linear-gradient(135deg, #06B6D4, #0EA5E9);

        }

        .bg-wallet {

            background: linear-gradient(135deg, #10B981, #34D399);

        }

        .bg-rating {

            background: linear-gradient(135deg, #F59E0B, #FBBF24);

        }

        /*--------------------------------*/

        .stat-number {

            font-size: 38px;

            font-weight: 700;

            color: #111827;

            line-height: 1;

        }

        .stat-title {

            margin-top: 10px;

            color: #6B7280;

            font-size: 15px;

        }

        .stat-change {

            margin-top: 18px;

            display: inline-flex;

            align-items: center;

            gap: 6px;

            padding: 6px 12px;

            border-radius: 50px;

            font-size: 13px;

            font-weight: 600;

        }

        .up {

            background: #ECFDF5;

            color: #10B981;

        }

        .down {

            background: #FEF2F2;

            color: #EF4444;

        }

        /*--------------------------------*/

        .stat-card:hover .stat-icon {

            transform: rotate(8deg) scale(1.08);

        }

        .stat-icon {

            transition: .35s;

        }

        /*--------------------------------*/

        .counter {

            transition: .3s;

        }

        /*--------------------------------*/

        .stat-glow {

            position: absolute;

            width: 120px;

            height: 120px;

            border-radius: 50%;

            right: -30px;

            bottom: -30px;

            opacity: .08;

            background: #4F46E5;

        }

        /*=========================================================
        LEFT SIDEBAR
    =========================================================*/

        .sidebar-card {

            background: #fff;

            border-radius: 24px;

            padding: 28px;

            margin-bottom: 25px;

            border: 1px solid #edf2f7;

            box-shadow: 0 15px 40px rgba(15, 23, 42, .08);

            transition: .35s;

        }

        .sidebar-card:hover {

            transform: translateY(-6px);

            box-shadow: 0 25px 55px rgba(79, 70, 229, .15);

        }

        /*--------------------------------*/

        .sidebar-title {

            font-size: 18px;

            font-weight: 700;

            margin-bottom: 22px;

        }

        /*--------------------------------*/

        .progress-wrapper {

            display: flex;

            justify-content: center;

            align-items: center;

            margin: 25px 0;

        }

        .progress-ring {

            width: 170px;

            height: 170px;

            border-radius: 50%;

            background: conic-gradient(#4F46E5 72%,

                    #E8ECF4 0%);

            display: flex;

            justify-content: center;

            align-items: center;

        }

        .progress-inner {

            width: 130px;

            height: 130px;

            border-radius: 50%;

            background: #fff;

            display: flex;

            flex-direction: column;

            justify-content: center;

            align-items: center;

        }

        .progress-inner h2 {

            font-size: 34px;

            font-weight: 700;

            color: #4F46E5;

        }

        .progress-inner span {

            color: #6B7280;

            font-size: 13px;

        }

        /*--------------------------------*/

        .profile-info {

            list-style: none;

            padding: 0;

            margin: 0;

        }

        .profile-info li {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 14px 0;

            border-bottom: 1px solid #F1F5F9;

        }

        .profile-info li:last-child {

            border: none;

        }

        .profile-info strong {

            color: #111827;

        }

        /*--------------------------------*/

        .level-card {

            margin-top: 25px;

            padding: 18px;

            border-radius: 18px;

            background: linear-gradient(135deg,

                    #4F46E5,

                    #6366F1);

            color: #fff;

        }

        .level-card h5 {

            margin-bottom: 5px;

        }

        .level-card small {

            opacity: .85;

        }

        /*--------------------------------*/

        .wallet-card {

            background: linear-gradient(135deg,

                    #10B981,

                    #34D399);

            border-radius: 20px;

            padding: 22px;

            color: #fff;

            margin-top: 20px;

        }

        .wallet-card h2 {

            font-size: 34px;

            font-weight: 700;

        }

        .wallet-card p {

            opacity: .85;

            margin-bottom: 0;

        }

        /*--------------------------------*/

        .rating-box {

            text-align: center;

            margin-top: 20px;

        }

        .rating-stars {

            font-size: 22px;

            color: #FBBF24;

        }

        .rating-number {

            font-size: 30px;

            font-weight: 700;

            margin-top: 8px;

        }

        /*--------------------------------*/

        .skill-list {

            margin-top: 20px;

        }

        .skill {

            display: inline-block;

            padding: 8px 18px;

            margin: 6px;

            border-radius: 50px;

            background: #EEF2FF;

            color: #4F46E5;

            font-size: 13px;

            font-weight: 600;

            transition: .3s;

        }

        .skill:hover {

            background: #4F46E5;

            color: #fff;

            transform: translateY(-3px);

        }

        /*--------------------------------*/

        .achievement {

            margin-top: 25px;

            text-align: center;

            padding: 22px;

            border-radius: 20px;

            background: #FFF8E7;

        }

        .achievement i {

            font-size: 42px;

            color: #F59E0B;

            margin-bottom: 12px;

        }

        .achievement h5 {

            font-weight: 700;

        }

        .achievement p {

            color: #6B7280;

            margin-bottom: 0;

        }


        /*=========================================================
        PROFILE FORM
    =========================================================*/

        .form-card {

            background: #fff;

            border-radius: 24px;

            padding: 35px;

            border: 1px solid #edf2f7;

            box-shadow: 0 15px 40px rgba(15, 23, 42, .08);

        }

        .form-title {

            font-size: 22px;

            font-weight: 700;

            margin-bottom: 30px;

        }

        /*--------------------------------*/

        .form-group {

            margin-bottom: 25px;

        }

        /*--------------------------------*/

        .form-label {

            display: block;

            font-weight: 600;

            margin-bottom: 10px;

            color: #374151;

        }

        /*--------------------------------*/

        .form-control,
        .form-select {

            height: 58px;

            border-radius: 18px;

            border: 2px solid #E8ECF4;

            background: #F8FAFC;

            padding: 0 20px;

            transition: .35s;

            box-shadow: none;

        }

        textarea.form-control {

            height: 150px;

            padding-top: 15px;

            resize: none;

        }

        /*--------------------------------*/

        .form-control:focus,
        .form-select:focus {

            border-color: #4F46E5;

            background: #fff;

            box-shadow:

                0 0 0 5px rgba(79, 70, 229, .08);

        }

        /*--------------------------------*/

        .input-icon {

            position: relative;

        }

        .input-icon i {

            position: absolute;

            left: 18px;

            top: 18px;

            color: #6B7280;

        }

        .input-icon input {

            padding-left: 50px;

        }

        /*--------------------------------*/

        .save-btn {

            padding: 14px 35px;

            border: none;

            border-radius: 50px;

            background:

                linear-gradient(135deg,

                    #4F46E5,

                    #06B6D4);

            color: #fff;

            font-weight: 600;

            transition: .35s;

        }

        .save-btn:hover {

            transform: translateY(-4px);

            box-shadow:

                0 15px 35px rgba(79, 70, 229, .30);

        }
    </style>
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

                <div class="stat-card">
                    <div class="stat-icon bg-project">
                        <i class="fas fa-folder-open"></i>
                    </div>

                    <div class="stat-number">
                        <span class="counter" data-target="24">0</span>
                    </div>

                    <div class="stat-title">
                        Total Projects
                    </div>

                    <div class="stat-change up">
                        <i class="fas fa-arrow-up"></i>
                        12% this month
                    </div>

                    <div class="stat-glow"></div>
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
