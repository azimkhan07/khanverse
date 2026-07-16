@extends('layouts.admin')

@section('content')
    <div class="page-header">

        <h4>{{ $homepage->title }}</h4>

    </div>

    <div class="card">

        <div class="card-body">

            <table class="table table-bordered">

                <tr>

                    <th width="200">Section</th>

                    <td>{{ ucfirst(str_replace('_', ' ', $homepage->section_key)) }}</td>

                </tr>

                <tr>

                    <th>Title</th>

                    <td>{{ $homepage->title }}</td>

                </tr>

                <tr>

                    <th>Subtitle</th>

                    <td>{{ $homepage->subtitle }}</td>

                </tr>

                <tr>

                    <th>Description</th>

                    <td>

                        {!! $homepage->description !!}

                    </td>

                </tr>

                <tr>

                    <th>Status</th>

                    <td>

                        @if ($homepage->status)
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

                <tr>

                    <th>Sort Order</th>

                    <td>{{ $homepage->sort_order }}</td>

                </tr>

            </table>

        </div>

    </div>
@endsection
