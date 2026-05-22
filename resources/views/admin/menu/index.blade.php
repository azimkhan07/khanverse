@extends('layouts.admin')

@section('content')

<div class="container">

    <h4>Menu Management</h4>

    <a href="{{ route('admin.menu.create') }}" class="btn btn-primary mb-3">
        Add Menu
    </a>

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>Title</th>
                <th>Route</th>
                <th>Icon</th>
                <th>Parent</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        @foreach($menus as $menu)

            <tr>
                <td><b>{{ $menu->title }}</b></td>
                <td>{{ $menu->route_name }}</td>
                <td>{{ $menu->icon }}</td>
                <td>-</td>
                <td>
                    <a href="{{ route('admin.menu.edit', $menu->id) }}" class="btn btn-sm btn-warning">Edit</a>

                    <form action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>

            @foreach($menu->children as $child)
            <tr>
                <td style="padding-left:40px;">↳ {{ $child->title }}</td>
                <td>{{ $child->route_name }}</td>
                <td>{{ $child->icon }}</td>
                <td>{{ $menu->title }}</td>
                <td>
                    <a href="{{ route('admin.menu.edit', $child->id) }}" class="btn btn-sm btn-warning">Edit</a>

                    <form action="{{ route('admin.menu.destroy', $child->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach

        @endforeach

        </tbody>

    </table>

</div>

@endsection