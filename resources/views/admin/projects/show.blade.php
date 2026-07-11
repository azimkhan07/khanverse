@extends('layouts.admin')

@section('content')
    <div class="page-header mb-4">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h4>{{ $project->title }}</h4>

                <small class="text-muted">

                    Project #{{ $project->id }}

                </small>

            </div>

            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">

                Back

            </a>

        </div>

    </div>


    <div class="row">

        <div class="col-md-8">

            @include('components.admin.project.project-info')

        </div>

        <div class="col-md-4">

            @include('components.admin.project.timeline')

        </div>

    </div>


    <div class="row mt-3">

        <div class="col-md-6">

            @include('components.admin.project.buyer-card')

        </div>

        <div class="col-md-6">

            @include('components.admin.project.seller-card')

        </div>

    </div>


    <div class="row mt-3">

        <div class="col-md-12">

            @include('components.admin.project.order-summary')

        </div>

    </div>


    <div class="row mt-3">

        <div class="col-md-12">

            @include('components.admin.project.files')

        </div>

    </div>


    <div class="row mt-3">

        <div class="col-md-12">

            @include('components.admin.project.chat-preview')

        </div>

    </div>


    <div class="row mt-3">

        <div class="col-md-6">

            @include('components.admin.project.activity-log')

        </div>

        <div class="col-md-6">

            @include('components.admin.project.violation-card')

        </div>

    </div>
@endsection
