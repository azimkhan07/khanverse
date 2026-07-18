@extends('layouts.admin')

@section('title','Edit SEO')

@section('content')

<div class="container-fluid">

    <form
        action="{{ route('admin.website.seo.update',$seo->id) }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        @include('components.admin.website.seo.form')

    </form>

</div>

@endsection
