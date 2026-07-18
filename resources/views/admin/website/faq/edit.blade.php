@extends('layouts.admin')

@section('title', 'Edit FAQ')

@section('content')

    <div class="container-fluid">

        <form action="{{ route('admin.website.faq.update', $faq->id) }}" method="POST">

            @csrf
            @method('PUT')

            @include('components.admin.website.faq.form')

        </form>

    </div>

@endsection
