@extends('layouts.admin')

@section('title', 'Create Banner')

@section('content')

    <div class="container-fluid">

        <form action="{{ route('admin.website.banners.store') }}" method="POST" enctype="multipart/form-data">

            @csrf

            @include('components.admin.website.banners.form')

        </form>

    </div>

@endsection
