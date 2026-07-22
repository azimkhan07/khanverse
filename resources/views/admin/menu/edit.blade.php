@extends('layouts.admin')

@section('content')
    <div class="container">

        <h4>Edit Menu</h4>

        <form method="POST" action="{{ route('admin.menu.update', $menu->id) }}">
            @csrf

            <input type="text" name="title" value="{{ $menu->title }}" class="form-control mb-2">

            <input type="text" name="icon" value="{{ $menu->icon }}" class="form-control mb-2">

            <input type="text" name="route_name" value="{{ $menu->route_name }}" class="form-control mb-2">

            <select name="parent_id" class="form-control mb-2">
                <option value="">Parent Menu</option>
                @foreach ($parents as $p)
                    <option value="{{ $p->id }}" {{ $menu->parent_id == $p->id ? 'selected' : '' }}>
                        {{ $p->title }}
                    </option>
                @endforeach
            </select>

            <select multiple name="roles[]" class="form-select">
                <option value="admin" {{ in_array('admin', $menu->roles ?? []) ? 'selected' : '' }}>
                    Admin
                </option>

                <option value="seller" {{ in_array('seller', $menu->roles ?? []) ? 'selected' : '' }}>
                    Seller
                </option>

                <option value="buyer" {{ in_array('buyer', $menu->roles ?? []) ? 'selected' : '' }}>
                    Buyer
                </option>
            </select>

            <input type="number" name="sort_order" value="{{ $menu->sort_order }}" class="form-control mb-2">

            <button class="btn btn-primary">Update</button>

        </form>

    </div>
@endsection
