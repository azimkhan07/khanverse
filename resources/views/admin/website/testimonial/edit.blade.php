@extends('layouts.admin')

@section('title', 'Edit Testimonial')

@section('content')

    <div class="container-fluid">

        <form action="{{ route('admin.website.testimonial.update', $testimonial->id) }}" method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            @include('components.admin.website.testimonial.form')

        </form>

    </div>

@endsection
