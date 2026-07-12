@extends('layouts.admin')

@section('title', 'Login Devices')

@section('content')

    <div class="container-fluid">

        @include('components.admin.users.devices.stats')

        @include('components.admin.users.devices.filters')

        @include('components.admin.users.devices.table')

    </div>

@endsection
