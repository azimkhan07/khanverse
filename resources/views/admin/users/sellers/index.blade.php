@extends('layouts.admin')

@section('title', 'Sellers')

@section('content')

    <div class="container-fluid">

        @include('components.admin.users.sellers.stats')

        @include('components.admin.users.sellers.filters')

        @include('components.admin.users.sellers.table')

    </div>

@endsection
