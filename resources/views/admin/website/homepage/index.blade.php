@extends('layouts.admin')

@section('title', 'Homepage Management')

@section('content')

    <div class="container-fluid">

        @include('components.admin.website.homepage.stats')

        @include('components.admin.website.homepage.filters')

        @include('components.admin.website.homepage.table')

    </div>

@endsection
