<div class="card mb-3">

    <div class="card-header">

        <h5>Payment Summary</h5>

    </div>

    <div class="card-body">

        <table class="table table-sm">

            <tr>

                <th>Amount</th>

                <td>

                    ₹{{ number_format($order->amount, 2) }}

                </td>

            </tr>

            <tr>

                <th>Platform Fee</th>

                <td>

                    ₹{{ number_format($order->platform_fee, 2) }}

                </td>

            </tr>

            <tr>

                <th>Seller Earnings</th>

                <td>

                    ₹{{ number_format($order->amount - $order->platform_fee, 2) }}

                </td>

            </tr>

        </table>

    </div>

</div>
