@extends('layouts.admin')

@section('title','Create Testimonial')

@section('content')

<div class="container-fluid">

    <form action="{{ route('admin.website.testimonial.store') }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf

        @include('components.admin.website.testimonial.form')

    </form>

</div>

@endsection
