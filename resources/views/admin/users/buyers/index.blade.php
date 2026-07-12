@extends('layouts.admin')

@section('title','Buyers')

@section('content')

<div class="container-fluid">

    @include('components.admin.users.buyers.stats')

    @include('components.admin.users.buyers.filters')

    @include('components.admin.users.buyers.table')

</div>

@endsection
