@extends('layouts.admin')

@section('title', 'Maintenance Mode')

@section('content')

    <div class="container-fluid">

        <form action="{{ route('admin.website.maintenance.update') }}" method="POST" enctype="multipart/form-data">

            @csrf

            @method('PUT')

            @include('components.admin.website.maintenance.form')

        </form>

    </div>

@endsection
