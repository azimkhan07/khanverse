@extends('layouts.admin')

@section('title','FAQ')

@section('content')

<div class="container-fluid">

    @include('components.admin.website.faq.stats')

    @include('components.admin.website.faq.filters')

    @include('components.admin.website.faq.table')

</div>

@endsection
