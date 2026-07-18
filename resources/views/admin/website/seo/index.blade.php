@extends('layouts.admin')

@section('title','SEO Management')

@section('content')

<div class="container-fluid">

    @include('components.admin.website.seo.stats')

    @include('components.admin.website.seo.filters')

    @include('components.admin.website.seo.table')

</div>

@endsection
