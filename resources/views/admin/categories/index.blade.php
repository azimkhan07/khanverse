@extends('layouts.admin')

@section('content')
    <x-card title="Categories" :createUrl="route('admin.categories.create')">

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Name</th>

                    <th>Slug</th>

                    <th>Icon</th>

                    <th>Status</th>

                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($categories as $category)
                    <tr>

                        <td>{{ $category->id }}</td>

                        <td>{{ $category->name }}</td>

                        <td>{{ $category->slug }}</td>

                        <td>{{ $category->icon }}</td>

                        <td>

                            <x-status-toggle :url="route('admin.categories.status', $category->id)" :checked="$category->status" />

                        </td>

                        <td>

                            <button class="btn btn-sm btn-primary openModalBtn"
                                data-url="{{ route('admin.categories.edit', $category->id) }}">
                                Edit
                            </button>

                            <button class="btn btn-sm btn-danger deleteBtn"
                                data-url="{{ route('admin.categories.destroy', $category->id) }}">
                                Delete
                            </button>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="text-center">
                            No Categories Found
                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

    </x-card>
@endsection
