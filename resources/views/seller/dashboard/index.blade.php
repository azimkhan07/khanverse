@extends('layouts.admin')

@section('title', 'Dashboard')

<style>
    .dashboard-card {
        transition: .3s;
    }

    .dashboard-card:hover {

        transform: translateY(-6px);

        box-shadow: 0 15px 35px rgba(0, 0, 0, .15);

    }
</style>

@section('content')

    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="mb-1">

                    Dashboard

                </h3>

                <p class="text-muted mb-0">

                    Welcome back, {{ auth()->user()->name }} 👋

                </p>

            </div>

            <div>

                <span class="badge bg-primary">

                    {{ now()->format('d M Y') }}

                </span>

            </div>

        </div>

        {{-- Statistics --}}
        @include('components.admin.dashboard.stats')

        {{-- Charts --}}
        @include('components.admin.dashboard.charts')

        <div class="row">

            <div class="col-lg-8">

                {{-- Recent Orders --}}
                @include('components.admin.dashboard.recent-orders')

            </div>

            <div class="col-lg-4">

                {{-- Quick Actions --}}
                @include('components.admin.dashboard.quick-actions')

            </div>

        </div>

        <div class="row mt-4">

            <div class="col-lg-6">

                {{-- Recent Users --}}
                @include('components.admin.dashboard.recent-users')

            </div>

            <div class="col-lg-6">

                {{-- System Status --}}
                @include('components.admin.dashboard.system-status')

            </div>

        </div>

        <div class="row mt-4">

            <div class="col-12">

                {{-- Activity Timeline --}}
                @include('components.admin.dashboard.activity')

            </div>

        </div>

    </div>

@endsection
