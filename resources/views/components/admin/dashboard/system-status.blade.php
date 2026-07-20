<div class="card shadow border-0 mb-4">

    <div class="card-header bg-white d-flex justify-content-between align-items-center">

        <h5 class="mb-0">

            <i class="fas fa-server text-success"></i>

            System Status

        </h5>

        <span class="badge bg-success">

            Live

        </span>

    </div>

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <span>

                <i class="fab fa-php text-primary me-2"></i>

                PHP Version

            </span>

            <strong>{{ PHP_VERSION }}</strong>

        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center mb-3">

            <span>

                <i class="fab fa-laravel text-danger me-2"></i>

                Laravel

            </span>

            <strong>{{ app()->version() }}</strong>

        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center mb-3">

            <span>

                <i class="fas fa-database text-info me-2"></i>

                Database

            </span>

            <span class="badge bg-success">

                Connected

            </span>

        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center mb-3">

            <span>

                <i class="fas fa-hdd text-warning me-2"></i>

                Storage

            </span>

            <span class="badge bg-primary">

                OK

            </span>

        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center mb-3">

            <span>

                <i class="fas fa-tools text-secondary me-2"></i>

                Maintenance

            </span>

            @php
                $maintenance = \App\Models\MaintenanceSetting::first();
            @endphp

            @if ($maintenance && $maintenance->status)
                <span class="badge bg-danger">

                    Enabled

                </span>
            @else
                <span class="badge bg-success">

                    Disabled

                </span>
            @endif

        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center">

            <span>

                <i class="fas fa-clock text-dark me-2"></i>

                Server Time

            </span>

            <strong id="serverClock"></strong>

        </div>

    </div>

</div>
@push('scripts')
    <script>
        function updateClock() {

            const now = new Date();

            const options = {

                day: '2-digit',
                month: 'short',
                year: 'numeric',

            };

            const date = now.toLocaleDateString('en-IN', options);

            const time = now.toLocaleTimeString('en-IN', {

                hour: '2-digit',

                minute: '2-digit',

                second: '2-digit',

                hour12: true

            });

            document.getElementById('serverClock').innerHTML = date + ' ' + time;

        }

        updateClock();

        setInterval(updateClock, 1000);
    </script>
@endpush
