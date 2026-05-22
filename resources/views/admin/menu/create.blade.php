@extends('layouts.admin')

@section('content')

<div class="container">

<h4>Add Menu</h4>

<form method="POST" action="{{ route('admin.menu.store') }}">
    @csrf

    <input type="text" name="title" placeholder="Title" class="form-control mb-2">

    <input type="text" name="icon" placeholder="Icon" class="form-control mb-2">

    <input type="text" name="route_name" placeholder="Route Name" class="form-control mb-2">

    <select name="parent_id" class="form-control mb-2">
        <option value="">Parent Menu</option>
        @foreach($parents as $p)
            <option value="{{ $p->id }}">{{ $p->title }}</option>
        @endforeach
    </select>

    <input type="number" name="sort_order" placeholder="Sort Order" class="form-control mb-2">

    <button class="btn btn-success">Save</button>

</form>

</div>

@endsection