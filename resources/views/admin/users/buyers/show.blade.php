@extends('layouts.admin')

@section('title', 'Buyer Details')

@section('content')

    <div class="container-fluid">

        <div class="row">

            <div class="col-md-4">

                <div class="card">

                    <div class="card-body text-center">

                        <img src="{{ $buyer->profile_image ? asset($buyer->profile_image) : asset('assets/images/user.png') }}"
                            width="120" class="rounded-circle mb-3">

                        <h4>

                            {{ $buyer->full_name }}

                        </h4>

                        <p>

                            {{ $buyer->company_name }}

                        </p>

                        <hr>

                        <p>

                            <strong>Email :</strong>

                            {{ $buyer->user->email }}

                        </p>

                        <p>

                            <strong>Phone :</strong>

                            {{ $buyer->user->phone }}

                        </p>

                        <p>

                            <strong>Country :</strong>

                            {{ $buyer->country }}

                        </p>

                        <p>

                            <strong>City :</strong>

                            {{ $buyer->city }}

                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-8">

                <div class="card">

                    <div class="card-header">

                        Buyer Information

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-4">

                                <h6>

                                    Projects

                                </h6>

                                <h3>

                                    {{ $buyer->projects->count() }}

                                </h3>

                            </div>

                            <div class="col-md-4">

                                <h6>

                                    Orders

                                </h6>

                                <h3>

                                    {{ $buyer->orders->count() }}

                                </h3>

                            </div>

                            <div class="col-md-4">

                                <h6>

                                    Devices

                                </h6>

                                <h3>

                                    {{ $buyer->user->devices->count() }}

                                </h3>

                            </div>

                        </div>

                        <hr>

                        <h5>

                            Recent Devices

                        </h5>

                        <table class="table">

                            <tr>

                                <th>Device</th>

                                <th>IP</th>

                                <th>Last Activity</th>

                            </tr>

                            @foreach ($buyer->user->devices as $device)
                                <tr>

                                    <td>

                                        {{ $device->device_name }}

                                    </td>

                                    <td>

                                        {{ $device->ip_address }}

                                    </td>

                                    <td>

                                        {{ $device->last_activity }}

                                    </td>

                                </tr>
                            @endforeach

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
