<div class="card">

    <div class="card-header">

        <h5>Sellers</h5>

    </div>

    <div class="table-responsive">

        <table class="table table-bordered table-hover">

            <thead>

                <tr>

                    <th>#</th>

                    <th>Profile</th>

                    <th>Name</th>

                    <th>Email</th>

                    <th>Phone</th>

                    <th>Services</th>

                    <th>Projects</th>

                    <th>Orders</th>

                    <th>Earning</th>

                    <th>Available</th>

                    <th>Verified</th>

                    <th>Status</th>

                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($sellers as $seller)
                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>

                            <img src="{{ $seller->profile_image ? asset($seller->profile_image) : asset('assets/images/user.png') }}"
                                width="45" class="rounded-circle">

                        </td>

                        <td>

                            {{ $seller->full_name }}

                            <br>

                            <small>{{ $seller->city }}</small>

                        </td>

                        <td>

                            {{ $seller->user->email }}

                        </td>

                        <td>

                            {{ $seller->user->phone }}

                        </td>

                        <td>

                            {{ $seller->services->count() }}

                        </td>

                        <td>

                            {{ $seller->projects->count() }}

                        </td>

                        <td>

                            {{ $seller->orders->count() }}

                        </td>

                        <td>

                            ₹ {{ number_format($seller->total_earning, 2) }}

                        </td>

                        <td>

                            @if ($seller->available_for_work)
                                <span class="badge badge-success">

                                    Available

                                </span>
                            @else
                                <span class="badge badge-danger">

                                    Busy

                                </span>
                            @endif

                        </td>

                        <td>

                            @if ($seller->user->is_verified)
                                <span class="badge badge-success">

                                    Verified

                                </span>
                            @else
                                <span class="badge badge-warning">

                                    Pending

                                </span>
                            @endif

                        </td>

                        <td>

                            @if ($seller->user->status)
                                <span class="badge badge-success">

                                    Active

                                </span>
                            @else
                                <span class="badge badge-danger">

                                    Inactive

                                </span>
                            @endif

                        </td>

                        <td>

                            <div class="dropdown">

                                <button class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">

                                    Action

                                </button>

                                <div class="dropdown-menu">

                                    <a class="dropdown-item"
                                        href="{{ route('admin.users.sellers.show', $seller->id) }}">

                                        View

                                    </a>

                                    <a class="dropdown-item" href="#">

                                        Projects

                                    </a>

                                    <a class="dropdown-item" href="#">

                                        Orders

                                    </a>

                                    <a class="dropdown-item" href="#">

                                        Services

                                    </a>

                                    <a class="dropdown-item" href="#">

                                        Devices

                                    </a>

                                </div>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="13" class="text-center">

                            No Seller Found

                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <div class="card-footer">

        {{ $sellers->links() }}

    </div>

</div>
