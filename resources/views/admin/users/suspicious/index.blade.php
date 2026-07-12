@extends('layouts.admin')

@section('title', 'Suspicious Users')

@section('content')

    <div class="container-fluid">

        @include('components.admin.users.suspicious.stats')

        @include('components.admin.users.suspicious.filters')

        @include('components.admin.users.suspicious.table')

    </div>

@endsection
