@extends('layouts.admin')

@section('title', 'Testimonials')

@section('content')

    <div class="container-fluid">

        @include('components.admin.website.testimonial.stats')

        @include('components.admin.website.testimonial.filters')

        @include('components.admin.website.testimonial.table')

    </div>

@endsection
