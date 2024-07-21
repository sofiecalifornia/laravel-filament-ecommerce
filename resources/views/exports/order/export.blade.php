<?php

/** @var \Domain\Shop\Order\Models\Order $order */

use App\Settings\SiteSettings;

?>

<table>

    <tr>
        <td>Order: {{ $order->receipt_number }}</td>
    </tr>

    <tr>
        <td>Total: {{ money($order->total_price*100) }}</td>
    </tr>

    <tr>
        <td>Created At: {{ $order->created_at->setTimezone(\Filament\Facades\Filament::auth()->user()->timezone) }}</td>
    </tr>

    <hr>

    @foreach($order->orderItems as $orderItem)

        <tr>

            <td>
                {{ $orderItem->name }} {{ $orderItem->minimum === null ? '' : "(min: $orderItem->minimum)" }}
                <br>{{ $orderItem->quantity }} x

                {{ money($orderItem->price*100) }}
            </td>

            <td>
                {{ money($orderItem->total_price*100) }}
            </td>

        </tr>

    @endforeach

</table>
