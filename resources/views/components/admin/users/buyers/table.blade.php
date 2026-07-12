<div class="card">

    <div class="card-header">

        <h5>Buyers</h5>

    </div>

    <div class="table-responsive">

        <table class="table table-bordered table-hover">

            <thead>

                <tr>

                    <th>#</th>

                    <th>Profile</th>

                    <th>Name</th>

                    <th>Username</th>

                    <th>Email</th>

                    <th>Phone</th>

                    <th>Location</th>

                    <th>Projects</th>

                    <th>Orders</th>

                    <th>Verified</th>

                    <th>Status</th>

                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($buyers as $buyer)
                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>

                            <img src="{{ $buyer->profile_image ? asset($buyer->profile_image) : asset('assets/images/user.png') }}"
                                width="45" class="rounded-circle">

                        </td>

                        <td>{{ $buyer->full_name }}</td>

                        <td>{{ $buyer->user->username ?? '-' }}</td>

                        <td>{{ $buyer->user->email ?? '-' }}</td>

                        <td>{{ $buyer->user->phone ?? '-' }}</td>

                        <td>

                            {{ $buyer->country }}

                            <br>

                            <small>{{ $buyer->city }}</small>

                        </td>

                        <td>

                            {{ $buyer->projects->count() }}

                        </td>

                        <td>

                            {{ $buyer->orders->count() }}

                        </td>

                        <td>

                            @if ($buyer->user?->is_verified)
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

                            @if ($buyer->user?->status)
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

                                <button class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">

                                    Action

                                </button>

                                <div class="dropdown-menu">

                                    <a href="{{ route('admin.users.buyers.show', $buyer->id) }}" class="dropdown-item">

                                        View Profile

                                    </a>

                                    <a href="#" class="dropdown-item">

                                        Projects

                                    </a>

                                    <a href="#" class="dropdown-item">

                                        Orders

                                    </a>

                                    <a href="#" class="dropdown-item">

                                        Devices

                                    </a>

                                </div>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="12" class="text-center">

                            No Buyers Found

                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <div class="card-footer">

        {{ $buyers->links() }}

    </div>

</div>
