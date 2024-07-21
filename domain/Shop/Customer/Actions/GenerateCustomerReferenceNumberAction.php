<?php

declare(strict_types=1);

namespace Domain\Shop\Customer\Actions;

use Illuminate\Support\Str;

final readonly class GenerateCustomerReferenceNumberAction
{
    public function execute(): string
    {
        return (string) Str::uuid();
    }
}
