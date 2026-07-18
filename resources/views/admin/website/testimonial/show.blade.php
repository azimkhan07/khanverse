@extends('layouts.admin')

@section('title', 'View Testimonial')

@section('content')

    <div class="container-fluid">

        <div class="card">

            <div class="card-body">

                @if ($testimonial->image)
                    <img src="{{ asset('storage/' . $testimonial->image) }}" width="120" class="img-thumbnail mb-3">
                @endif

                <h3>

                    {{ $testimonial->name }}

                </h3>

                <p>

                    {{ $testimonial->designation }}

                    @if ($testimonial->company)
                        - {{ $testimonial->company }}
                    @endif

                </p>

                <hr>

                {!! $testimonial->review !!}

                <hr>

                <table class="table">

                    <tr>

                        <th>Rating</th>

                        <td>{{ $testimonial->rating }}/5 ⭐</td>

                    </tr>

                    <tr>

                        <th>Status</th>

                        <td>

                            @if ($testimonial->status)
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

                        <td>{{ $testimonial->sort_order }}</td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

@endsection
