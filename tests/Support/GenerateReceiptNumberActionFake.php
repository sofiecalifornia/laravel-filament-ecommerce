<?php

declare(strict_types=1);

namespace Tests\Support;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Order\Actions\GenerateReceiptNumberAction;
use Illuminate\Support\Str;

readonly class GenerateReceiptNumberActionFake extends GenerateReceiptNumberAction
{
    #[\Override]
    public function execute(Branch $branch): string
    {
        return (string) Str::uuid();
    }
}
