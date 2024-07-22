<?php

declare(strict_types=1);

namespace Support\ReceiptPrinter;

use Illuminate\Support\ServiceProvider;

class PrinterServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/receipt-printer.php', 'support.receipt-printer');
    }
}
