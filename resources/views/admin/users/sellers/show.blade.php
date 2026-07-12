@extends('layouts.admin')

@section('title', 'Seller Details')

@section('content')

    <div class="container-fluid">

        <div class="row">

            <div class="col-md-4">

                <div class="card">

                    <div class="card-body text-center">

                        <img src="{{ $seller->profile_image ? asset($seller->profile_image) : asset('assets/images/user.png') }}"
                            width="120" class="rounded-circle mb-3">

                        <h4>

                            {{ $seller->full_name }}

                        </h4>

                        <hr>

                        <p>

                            <strong>Email :</strong>

                            {{ $seller->user->email }}

                        </p>

                        <p>

                            <strong>Phone :</strong>

                            {{ $seller->user->phone }}

                        </p>

                        <p>

                            <strong>Country :</strong>

                            {{ $seller->country }}

                        </p>

                        <p>

                            <strong>City :</strong>

                            {{ $seller->city }}

                        </p>

                        <p>

                            <strong>Hourly Rate :</strong>

                            ₹ {{ $seller->hourly_rate }}

                        </p>

                        <p>

                            <strong>Experience :</strong>

                            {{ $seller->experience_level }}

                        </p>

                        <p>

                            <strong>Available :</strong>

                            @if ($seller->available_for_work)
                                <span class="badge badge-success">

                                    Yes

                                </span>
                            @else
                                <span class="badge badge-danger">

                                    No

                                </span>
                            @endif

                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-8">

                <div class="card">

                    <div class="card-header">

                        Seller Dashboard

                    </div>

                    <div class="card-body">

                        <div class="row text-center">

                            <div class="col-md-3">

                                <h6>Services</h6>

                                <h3>

                                    {{ $seller->services->count() }}

                                </h3>

                            </div>

                            <div class="col-md-3">

                                <h6>Projects</h6>

                                <h3>

                                    {{ $seller->projects->count() }}

                                </h3>

                            </div>

                            <div class="col-md-3">

                                <h6>Orders</h6>

                                <h3>

                                    {{ $seller->orders->count() }}

                                </h3>

                            </div>

                            <div class="col-md-3">

                                <h6>Earnings</h6>

                                <h3>

                                    ₹ {{ number_format($seller->total_earning, 2) }}

                                </h3>

                            </div>

                        </div>

                        <hr>

                        <h5>

                            Skills

                        </h5>

                        <p>

                            {{ $seller->skills }}

                        </p>

                        <hr>

                        <h5>

                            Recent Login Devices

                        </h5>

                        <table class="table">

                            <thead>

                                <tr>

                                    <th>Device</th>

                                    <th>Browser</th>

                                    <th>IP</th>

                                    <th>Last Activity</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($seller->user->devices as $device)
                                    <tr>

                                        <td>{{ $device->device_name }}</td>

                                        <td>{{ $device->browser }}</td>

                                        <td>{{ $device->ip_address }}</td>

                                        <td>{{ $device->last_activity }}</td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
