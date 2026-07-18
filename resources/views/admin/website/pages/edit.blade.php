@extends('layouts.admin')

@section('title', 'Edit Page')

@section('content')

    <div class="container-fluid">

        <form action="{{ route('admin.website.pages.update', $page->id) }}" method="POST" enctype="multipart/form-data">

            @csrf
            @method('PUT')

            @include('components.admin.website.pages.form')

        </form>

    </div>

@endsection
