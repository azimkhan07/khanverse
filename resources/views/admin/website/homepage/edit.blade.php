@extends('layouts.admin')

@section('title', 'Edit Homepage Section')

@section('content')

    <div class="container-fluid">

        <form action="{{ route('admin.website.homepage.update', $homepage->id) }}" method="POST" enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-lg-8">

                    @include('components.admin.website.homepage.basic-information')

                </div>

                <div class="col-lg-4">

                    @include('components.admin.website.homepage.display-settings')

                    @include('components.admin.website.homepage.buttons')

                    @include('components.admin.website.homepage.media')

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <button class="btn btn-primary w-100">

                                Update Section

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>

@endsection
