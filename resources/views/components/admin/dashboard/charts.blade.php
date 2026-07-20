<div class="row g-4 mb-4 mt-3">

    {{-- Monthly Orders --}}

    <div class="col-lg-8">

        <div class="dashboard-chart-card">

            <div class="dashboard-chart-header">

                <div>

                    <h5>

                        <i class="fas fa-chart-line me-2"></i>

                        Monthly Orders

                    </h5>

                    <small>

                        Orders analytics of current year

                    </small>

                </div>

                <div>

                    <select class="form-select shadow-none">

                        <option>2026</option>
                        <option>2025</option>

                    </select>

                </div>

            </div>

            <div class="dashboard-chart-body">

                <canvas id="ordersChart"></canvas>

            </div>

        </div>

    </div>

    {{-- Revenue --}}

    <div class="col-lg-4">

        <div class="dashboard-chart-card revenue-card">

            <div class="dashboard-chart-header">

                <div>

                    <h5>

                        <i class="fas fa-wallet me-2"></i>

                        Revenue

                    </h5>

                    <small>

                        Overall Revenue

                    </small>

                </div>

            </div>

            <div class="dashboard-chart-body">

                <canvas id="revenueChart"></canvas>

            </div>

            <div class="row text-center mt-4">

                <div class="col">

                    <h4 class="text-success">

                        ₹ {{ number_format($totalRevenue ?? 0) }}

                    </h4>

                    <small>Total Revenue</small>

                </div>

            </div>

        </div>

    </div>

</div>

<style>
    .dashboard-chart-card {

        background: #fff;

        border-radius: 20px;

        padding: 25px;

        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);

        transition: .35s;

        overflow: hidden;

        position: relative;

    }

    .dashboard-chart-card:hover {

        transform: translateY(-6px);

        box-shadow: 0 20px 40px rgba(0, 0, 0, .15);

    }

    .dashboard-chart-card::before {

        content: '';

        position: absolute;

        top: 0;

        left: 0;

        width: 100%;

        height: 4px;

        background: linear-gradient(90deg, #4F46E5, #06B6D4, #10B981);

    }

    .dashboard-chart-header {

        display: flex;

        justify-content: space-between;

        align-items: center;

        margin-bottom: 20px;

    }

    .dashboard-chart-header h5 {

        margin: 0;

        font-weight: 700;

    }

    .dashboard-chart-header small {

        color: #888;

    }

    .dashboard-chart-header .form-select {

        border-radius: 12px;

        width: 120px;

    }

    .dashboard-chart-body {

        position: relative;

        height: 350px;

    }

    .revenue-card .dashboard-chart-body {

        height: 250px;

    }
</style>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ordersCtx = document.getElementById('ordersChart');

        new Chart(ordersCtx, {

            type: 'line',

            data: {

                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],

                datasets: [{

                    label: 'Orders',

                    data: [20, 35, 18, 42, 51, 63, 72, 81, 69, 90, 120, 140],

                    fill: true,

                    borderWidth: 3,

                    borderColor: '#4F46E5',

                    backgroundColor: 'rgba(79,70,229,.12)',

                    pointBackgroundColor: '#4F46E5',

                    pointRadius: 5,

                    tension: .4

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {

                        display: false

                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        grid: {

                            color: '#eee'

                        }

                    },

                    x: {

                        grid: {

                            display: false

                        }

                    }

                }

            }

        });

        const revenueCtx = document.getElementById('revenueChart');

        new Chart(revenueCtx, {

            type: 'doughnut',

            data: {

                labels: ['Completed', 'Pending'],

                datasets: [{

                    data: [75, 25],

                    backgroundColor: [

                        '#10B981',

                        '#F59E0B'

                    ],

                    borderWidth: 0,

                    hoverOffset: 15

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '72%',

                plugins: {

                    legend: {

                        position: 'bottom'

                    }

                }

            }

        });
    </script>
@endpush
