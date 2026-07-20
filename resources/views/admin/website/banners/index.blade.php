@extends('layouts.admin')

@section('title', 'Banner Management')

@section('content')

    <div class="container-fluid">

        @include('components.admin.website.banners.stats')

        @include('components.admin.website.banners.filters')

        @include('components.admin.website.banners.table')

    </div>

@endsection
