<div class="card">

    <div class="card-header">

        <h5>Timeline</h5>

    </div>

    <div class="card-body">

        <ul class="list-group">

            <li class="list-group-item">

                Created

                <span class="float-end">

                    {{ $order->created_at }}

                </span>

            </li>

            <li class="list-group-item">

                Delivery

                <span class="float-end">

                    {{ $order->delivery_date }}

                </span>

            </li>

            <li class="list-group-item">

                Updated

                <span class="float-end">

                    {{ $order->updated_at }}

                </span>

            </li>

        </ul>

    </div>

</div>
