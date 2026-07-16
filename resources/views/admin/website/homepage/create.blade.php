@extends('layouts.admin')

@section('title', 'Create Homepage Section')

@section('content')

    <div class="container-fluid">

        <form action="{{ route('admin.website.homepage.store') }}" method="POST" enctype="multipart/form-data">

            @csrf

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

                            <button class="btn btn-success w-100">

                                Save Section

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>

@endsection
