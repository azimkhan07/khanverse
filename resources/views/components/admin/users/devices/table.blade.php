<div class="card">

    <div class="card-header">

        <h5>Login Devices</h5>

    </div>

    <div class="table-responsive">

        <table class="table table-bordered table-hover">

            <thead>

                <tr>

                    <th>User</th>

                    <th>Device</th>

                    <th>Browser</th>

                    <th>Platform</th>

                    <th>IP</th>

                    <th>Current</th>

                    <th>Last Activity</th>

                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

                @foreach ($devices as $device)
                    <tr>

                        <td>

                            {{ $device->user->username }}

                        </td>

                        <td>

                            {{ $device->device_name }}

                        </td>

                        <td>

                            {{ $device->browser }}

                        </td>

                        <td>

                            {{ $device->platform }}

                        </td>

                        <td>

                            {{ $device->ip_address }}

                        </td>

                        <td>

                            @if ($device->is_current_device)
                                <span class="badge badge-success">

                                    Current

                                </span>
                            @endif

                        </td>

                        <td>

                            {{ $device->last_activity }}

                        </td>

                        <td>

                            <a href="#" class="btn btn-danger btn-sm">

                                Logout

                            </a>

                        </td>

                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>

    <div class="card-footer">

        {{ $devices->links() }}

    </div>

</div>
