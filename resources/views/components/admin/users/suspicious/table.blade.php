<div class="card">

    <div class="card-header">

        <h5>Suspicious Users</h5>

    </div>

    <div class="table-responsive">

        <table class="table table-hover table-bordered">

            <thead>

                <tr>

                    <th>User</th>

                    <th>Role</th>

                    <th>Email</th>

                    <th>Phone</th>

                    <th>Same IP</th>

                    <th>Verified</th>

                    <th>Status</th>

                    <th>Risk</th>

                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

                @foreach ($users as $user)
                    <tr>

                        <td>{{ $user->username }}</td>

                        <td>{{ ucfirst($user->role) }}</td>

                        <td>{{ $user->email }}</td>

                        <td>{{ $user->phone }}</td>

                        <td>

                            {{ $user->same_ip_accounts }}

                        </td>

                        <td>

                            @if ($user->is_verified)
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

                            @if ($user->status)
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

                            @if ($user->same_ip_accounts >= 2)
                                <span class="badge badge-danger">

                                    High

                                </span>
                            @else
                                <span class="badge badge-success">

                                    Normal

                                </span>
                            @endif

                        </td>

                        <td>

                            <a class="btn btn-info btn-sm">

                                Inspect

                            </a>

                        </td>

                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>

    <div class="card-footer">

        {{ $users->links() }}

    </div>

</div>
