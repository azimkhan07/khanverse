@extends('buyer.layouts.app')

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
                    Buyer Dashboard
                </h3>

                <p class="text-muted mb-0">
                    Welcome back,
                    {{ auth()->user()->name }} 👋
                </p>

            </div>

            <div>

                <span class="badge bg-primary">
                    {{ now()->format('d M Y') }}
                </span>

            </div>

        </div>

        {{-- Statistics --}}
        @include('components.buyer.dashboard.stats')

        <div class="row mt-4">

            <div class="col-lg-8">

                {{-- Recent Orders --}}
                @include('components.buyer.dashboard.recent-orders')

            </div>

            <div class="col-lg-4">

                {{-- Wallet --}}
                @include('components.buyer.dashboard.wallet')

            </div>

        </div>

        <div class="row mt-4">

            <div class="col-lg-8">

                {{-- Active Projects --}}
                @include('components.buyer.dashboard.projects')

            </div>

            <div class="col-lg-4">

                {{-- Recent Reviews --}}
                @include('components.buyer.dashboard.reviews')

            </div>

        </div>

    </div>

@endsection
