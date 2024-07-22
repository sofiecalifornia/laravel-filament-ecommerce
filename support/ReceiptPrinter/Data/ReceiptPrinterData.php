<?php

declare(strict_types=1);

namespace Support\ReceiptPrinter\Data;

use Mike42\Escpos\Printer;
use Spatie\Cloneable\Cloneable;

// https://github.com/charlieuki/receipt-printer
readonly class ReceiptPrinterData
{
    use Cloneable;

    public StoreData $store;

    /**
     * @param  array<int, ItemData>  $items
     */
    public function __construct(
        public array $items = [],
        public float $taxPercentage = 0,
        public string $transactionId = '',
        public ?string $logo = null,
        public array $qrCode = [],
    ) {
        $this->store = new StoreData('', '', '', '', '', '');
    }

    public function store(StoreData $store): self
    {
        return $this->with(store: $store);
    }

    /**
     * @param  array<int, ItemData>  $items
     */
    public function items(array $items): self
    {
        return $this->with(items: $items);
    }

    public function logo(string $logo): self
    {
        return $this->with(logo: $logo);
    }

    public function transactionId(string $transactionId): self
    {
        return $this->with(transactionId: $transactionId);
    }

    public function taxPercentage(float $taxPercentage): self
    {
        return $this->with(taxPercentage: $taxPercentage);
    }

    public function qrCode(array $qrCode): self
    {
        return $this->with(qrCode: $qrCode);
    }
}
