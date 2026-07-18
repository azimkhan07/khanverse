@extends('layouts.admin')

@section('title', 'View FAQ')

@section('content')

    <div class="container-fluid">

        <div class="card">

            <div class="card-body">

                <h3>{{ $faq->question }}</h3>

                <hr>

                {!! $faq->answer !!}

                <hr>

                <table class="table">

                    <tr>

                        <th>Status</th>

                        <td>

                            @if ($faq->status)
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

                        <td>{{ $faq->sort_order }}</td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

@endsection
