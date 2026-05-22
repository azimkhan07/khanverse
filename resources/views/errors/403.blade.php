@extends('layouts.admin')

@section('content')

<div class="text-center mt-5">

    <h1>403</h1>

    <h4>Permission Denied</h4>

    <a href="{{ url()->previous() }}" class="btn btn-primary">
        Go Back
    </a>

</div>

@endsection