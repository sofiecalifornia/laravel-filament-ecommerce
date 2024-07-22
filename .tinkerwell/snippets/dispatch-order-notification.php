<?php

use Domain\Shop\Order\DataTransferObjects\OrderPipelineData;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Pipes\OrderCreated\NotificationPipe;
use Illuminate\Support\Facades\Pipeline;

$order = Order::where('receipt_number', 'ORDERBRANCH_12311190003')
    ->first();

Pipeline::send(new OrderPipelineData($order))
    ->through([
        NotificationPipe::class,
    ])
    ->thenReturn();
