@extends('layouts.admin')

@section('title', 'Create Page')

@section('content')

    <div class="container-fluid">

        <form action="{{ route('admin.website.pages.store') }}" method="POST" enctype="multipart/form-data">

            @csrf

            @include('components.admin.website.pages.form')

        </form>

    </div>

@endsection
