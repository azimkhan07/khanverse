@extends('layouts.admin')

@section('title', 'Create SEO')

@section('content')

    <div class="container-fluid">

        <form action="{{ route('admin.website.seo.store') }}" method="POST" enctype="multipart/form-data">

            @csrf

            @include('components.admin.website.seo.form')

        </form>

    </div>

@endsection
