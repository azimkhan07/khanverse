@extends('layouts.admin')

@section('title', 'Pages')

@section('content')

    <div class="container-fluid">

        @include('components.admin.website.pages.stats')

        @include('components.admin.website.pages.filters')

        @include('components.admin.website.pages.table')

    </div>

@endsection
