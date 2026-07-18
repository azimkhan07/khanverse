@extends('layouts.admin')

@section('title', 'View Page')

@section('content')

    <div class="container-fluid">

        <div class="card">

            <div class="card-body">

                <h3>{{ $page->title }}</h3>

                <hr>

                @if ($page->banner_image)
                    <img src="{{ asset('storage/' . $page->banner_image) }}" class="img-fluid mb-3" width="250">
                @endif

                <h5>Description</h5>

                {!! $page->description !!}

                <hr>

                <table class="table">

                    <tr>

                        <th>Slug</th>

                        <td>{{ $page->slug }}</td>

                    </tr>

                    <tr>

                        <th>Status</th>

                        <td>

                            @if ($page->status)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif

                        </td>

                    </tr>

                    <tr>

                        <th>Meta Title</th>

                        <td>{{ $page->meta_title }}</td>

                    </tr>

                    <tr>

                        <th>Keywords</th>

                        <td>{{ $page->meta_keywords }}</td>

                    </tr>

                    <tr>

                        <th>Description</th>

                        <td>{{ $page->meta_description }}</td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

@endsection
