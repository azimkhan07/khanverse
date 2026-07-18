@extends('layouts.admin')

@section('title', 'Create FAQ')

@section('content')

    <div class="container-fluid">

        <form action="{{ route('admin.website.faq.store') }}" method="POST">

            @csrf

            @include('components.admin.website.faq.form')

        </form>

    </div>

@endsection
