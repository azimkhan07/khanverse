<div class="card shadow-sm border-0">

    <div class="card-header">

        <h5>

            Order Summary

        </h5>

    </div>

    <div class="card-body">

        @if ($project->order)
            <table class="table">

                <tr>

                    <th>Amount</th>

                    <td>

                        ₹{{ number_format($project->order->amount, 2) }}

                    </td>

                </tr>

                <tr>

                    <th>Platform Fee</th>

                    <td>

                        ₹{{ number_format($project->order->platform_fee, 2) }}

                    </td>

                </tr>

                <tr>

                    <th>Status</th>

                    <td>

                        {{ ucfirst($project->order->status) }}

                    </td>

                </tr>

            </table>
        @else
            <div class="alert alert-warning">

                No Order Created Yet.

            </div>
        @endif

    </div>

</div>
