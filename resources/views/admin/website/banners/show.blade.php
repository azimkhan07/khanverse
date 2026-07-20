@extends('layouts.admin')

@section('title', 'Banner Details')

@section('content')

    <div class="container-fluid">

        <div class="card">

            <div class="card-body">

                <table class="table table-bordered">

                    <tr>

                        <th>Title</th>

                        <td>{{ $banner->title }}</td>

                    </tr>

                    <tr>

                        <th>Link</th>

                        <td>{{ $banner->link }}</td>

                    </tr>

                    <tr>

                        <th>Position</th>

                        <td>{{ ucfirst(str_replace('_', ' ', $banner->position)) }}</td>

                    </tr>

                    <tr>

                        <th>Status</th>

                        <td>

                            @if ($banner->status)
                                <span class="badge badge-success">

                                    Active

                                </span>
                            @else
                                <span class="badge badge-danger">

                                    Inactive

                                </span>
                            @endif

                        </td>

                    </tr>

                </table>

                @if ($banner->image)
                    <img src="{{ asset('storage/' . $banner->image) }}" class="img-thumbnail" width="250">
                @endif

            </div>

        </div>

    </div>

@endsection
