@extends('layouts.admin')

@section('title', 'Edit Banner')

@section('content')

    <div class="container-fluid">

        <form action="{{ route('admin.website.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">

            @csrf
            @method('PUT')

            @include('components.admin.website.banners.form')

        </form>

    </div>

@endsection
