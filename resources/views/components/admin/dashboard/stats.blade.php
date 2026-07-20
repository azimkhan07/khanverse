<div class="row g-4">

    @php

        $cards = [
            [
                'title' => 'Total Sellers',
                'count' => $totalSellers ?? 0,
                'icon' => 'fas fa-store',
                'color' => 'primary',
                'trend' => '+12%',
            ],

            [
                'title' => 'Total Buyers',
                'count' => $totalBuyers ?? 0,
                'icon' => 'fas fa-users',
                'color' => 'success',
                'trend' => '+8%',
            ],

            [
                'title' => 'Services',
                'count' => $totalServices ?? 0,
                'icon' => 'fas fa-cogs',
                'color' => 'warning',
                'trend' => '+4%',
            ],

            [
                'title' => 'Orders',
                'count' => $totalOrders ?? 0,
                'icon' => 'fas fa-shopping-cart',
                'color' => 'danger',
                'trend' => '+15%',
            ],

            [
                'title' => 'Projects',
                'count' => $totalProjects ?? 0,
                'icon' => 'fas fa-project-diagram',
                'color' => 'info',
                'trend' => '+10%',
            ],

            [
                'title' => 'Revenue',
                'count' => '₹ ' . number_format($totalRevenue ?? 0),
                'icon' => 'fas fa-rupee-sign',
                'color' => 'dark',
                'trend' => '+18%',
            ],

            [
                'title' => 'Pending',
                'count' => $pendingOrders ?? 0,
                'icon' => 'fas fa-clock',
                'color' => 'secondary',
                'trend' => '-2%',
            ],

            [
                'title' => 'Completed',
                'count' => $completedOrders ?? 0,
                'icon' => 'fas fa-check-circle',
                'color' => 'success',
                'trend' => '+20%',
            ],
        ];

    @endphp

    @foreach ($cards as $card)
        <div class="col-xl-3 col-lg-4 col-md-6">

            <div class="dashboard-card dashboard-{{ $card['color'] }}">

                <div class="card-overlay"></div>

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <span class="dashboard-title">

                            {{ $card['title'] }}

                        </span>

                        <h2 class="dashboard-number mt-3">

                            {{ $card['count'] }}

                        </h2>

                    </div>

                    <div class="dashboard-icon">

                        <i class="{{ $card['icon'] }}"></i>

                    </div>

                </div>

                <div class="dashboard-footer">

                    <span class="trend">

                        <i class="fas fa-arrow-up"></i>

                        {{ $card['trend'] }}

                    </span>

                    <span>

                        This Month

                    </span>

                </div>

                <div class="progress mt-3">

                    <div class="progress-bar bg-white" style="width:75%"></div>

                </div>

            </div>

        </div>
    @endforeach

</div>

<style>
    .dashboard-card {

        position: relative;

        overflow: hidden;

        border-radius: 20px;

        padding: 25px;

        color: #fff;

        transition: .35s;

        box-shadow: 0 12px 30px rgba(0, 0, 0, .12);

        min-height: 185px;

    }

    .dashboard-card:hover {

        transform: translateY(-8px);

        box-shadow: 0 25px 45px rgba(0, 0, 0, .18);

    }

    .dashboard-card:hover .dashboard-icon {

        transform: rotate(10deg) scale(1.15);

    }

    .dashboard-card:hover .card-overlay {

        opacity: 1;

    }

    .card-overlay {

        position: absolute;

        inset: 0;

        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, .25), transparent);

        transform: translateX(-120%);

        animation: shine 5s infinite;

        opacity: .7;

    }

    @keyframes shine {

        100% {

            transform: translateX(120%);

        }

    }

    .dashboard-title {

        font-size: 14px;

        opacity: .9;

        letter-spacing: .5px;

    }

    .dashboard-number {

        font-size: 34px;

        font-weight: 700;

    }

    .dashboard-icon {

        width: 65px;

        height: 65px;

        border-radius: 18px;

        display: flex;

        align-items: center;

        justify-content: center;

        background: rgba(255, 255, 255, .18);

        font-size: 28px;

        transition: .4s;

        backdrop-filter: blur(10px);

    }

    .dashboard-footer {

        display: flex;

        justify-content: space-between;

        align-items: center;

        margin-top: 18px;

        font-size: 13px;

    }

    .trend {

        font-weight: 600;

    }

    .progress {

        height: 6px;

        background: rgba(255, 255, 255, .2);

        border-radius: 20px;

    }

    .progress-bar {

        border-radius: 20px;

    }

    .dashboard-primary {

        background: linear-gradient(135deg, #4f46e5, #6366f1);

    }

    .dashboard-success {

        background: linear-gradient(135deg, #10b981, #34d399);

    }

    .dashboard-warning {

        background: linear-gradient(135deg, #f59e0b, #fbbf24);

    }

    .dashboard-danger {

        background: linear-gradient(135deg, #ef4444, #f87171);

    }

    .dashboard-info {

        background: linear-gradient(135deg, #06b6d4, #22d3ee);

    }

    .dashboard-dark {

        background: linear-gradient(135deg, #1f2937, #374151);

    }

    .dashboard-secondary {

        background: linear-gradient(135deg, #64748b, #94a3b8);

    }
</style>
