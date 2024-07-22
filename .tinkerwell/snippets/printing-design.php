<?php

use Support\ReceiptPrinter\Data\ItemData;
use Support\ReceiptPrinter\Data\ReceiptPrinterData;
use Support\ReceiptPrinter\Data\StoreData;
use Support\ReceiptPrinter\ReceiptPrinter;

$transaction_id = 'TX123ABC456';

$data = (new ReceiptPrinterData())
    ->items(
        [
            new ItemData(
                'French Fries (tera)',
                2,
                money(750_00),
                money(750_00)->multiply(2),
            ),
            new ItemData(
                'Roasted Milk Tea (large)',
                1,
                money(240_00),
                money(240_00)
            ),
            new ItemData(
                'Honey Lime (large)',
                3,
                money(100_00),
                money(100_00)->multiply(3),
            ),
            new ItemData(
                'Jasmine Tea (grande)',
                10,
                money(80_00),
                money(80)->multiply(10),
            ),
        ],
    )
    ->store(
        new StoreData(
            'TESTMID',
            'YOURMART',
            'Mart Address',
            '1234567890',
            'yourmart@email.com',
            'yourmart.com'
        ),
    )
    ->transactionId($transaction_id)
//    ->logo('logo.png')
    ->qrCode([
        'tid' => $transaction_id,
    ])//    ->taxPercentage(10)
;

(new ReceiptPrinter($data))->send();
