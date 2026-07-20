<div class="dashboard-table-card">

    <div class="dashboard-table-header">

        <div>

            <h5>

                <i class="fas fa-shopping-bag me-2"></i>

                Recent Orders

            </h5>

            <small>

                Latest orders received

            </small>

        </div>

        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm rounded-pill px-4">

            View All

        </a>

    </div>

    <div class="table-responsive">

        <table class="table align-middle dashboard-table mb-0">

            <thead>

                <tr>

                    <th>#</th>

                    <th>Buyer</th>

                    <th>Seller</th>

                    <th>Amount</th>

                    <th>Status</th>

                    <th>Date</th>

                </tr>

            </thead>

            <tbody>

                @forelse($recentOrders ?? [] as $order)
                    <tr>

                        <td>

                            <strong>

                                #{{ $order->id }}

                            </strong>

                        </td>

                        <td>

                            <div class="d-flex align-items-center">

                                <img src="https://ui-avatars.com/api/?name={{ urlencode($order->buyer->name ?? 'Buyer') }}&background=4F46E5&color=fff"
                                    class="rounded-circle me-3" width="42" height="42">

                                <div>

                                    <strong>

                                        {{ $order->buyer->name ?? '-' }}

                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        Buyer

                                    </small>

                                </div>

                            </div>

                        </td>

                        <td>

                            <div class="d-flex align-items-center">

                                <img src="https://ui-avatars.com/api/?name={{ urlencode($order->seller->name ?? 'Seller') }}&background=10B981&color=fff"
                                    class="rounded-circle me-3" width="42" height="42">

                                <div>

                                    <strong>

                                        {{ $order->seller->name ?? '-' }}

                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        Seller

                                    </small>

                                </div>

                            </div>

                        </td>

                        <td>

                            <strong class="text-success">

                                ₹ {{ number_format($order->total_amount ?? 0, 2) }}

                            </strong>

                        </td>

                        <td>

                            @php

                                $statusClass = [
                                    'pending' => 'warning',

                                    'completed' => 'success',

                                    'processing' => 'info',

                                    'cancelled' => 'danger',
                                ];

                            @endphp

                            <span class="badge rounded-pill bg-{{ $statusClass[$order->status] ?? 'secondary' }}">

                                {{ ucfirst($order->status) }}

                            </span>

                        </td>

                        <td>

                            {{ $order->created_at->format('d M Y') }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6">

                            <div class="text-center py-5">

                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>

                                <h6>

                                    No Orders Found

                                </h6>

                                <small class="text-muted">

                                    Recent orders will appear here.

                                </small>

                            </div>

                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

<style>
    .dashboard-table-card {

        background: #fff;

        border-radius: 20px;

        padding: 25px;

        box-shadow: 0 12px 30px rgba(0, 0, 0, .08);

        transition: .35s;

    }

    .dashboard-table-card:hover {

        transform: translateY(-5px);

        box-shadow: 0 20px 45px rgba(0, 0, 0, .15);

    }

    .dashboard-table-header {

        display: flex;

        justify-content: space-between;

        align-items: center;

        margin-bottom: 25px;

    }

    .dashboard-table-header h5 {

        font-weight: 700;

        margin: 0;

    }

    .dashboard-table-header small {

        color: #888;

    }

    .dashboard-table {

        border-collapse: separate;

        border-spacing: 0 12px;

    }

    .dashboard-table thead th {

        border: none;

        color: #777;

        font-size: 13px;

        text-transform: uppercase;

        font-weight: 700;

    }

    .dashboard-table tbody tr {

        background: #f8fafc;

        transition: .3s;

    }

    .dashboard-table tbody tr:hover {

        background: #eef4ff;

        transform: scale(1.01);

    }

    .dashboard-table tbody td {

        border: none;

        padding: 16px;

        vertical-align: middle;

    }

    .dashboard-table tbody tr td:first-child {

        border-radius: 12px 0 0 12px;

    }

    .dashboard-table tbody tr td:last-child {

        border-radius: 0 12px 12px 0;

    }

    .badge {

        font-size: 12px;

        padding: 8px 14px;

    }
</style>
