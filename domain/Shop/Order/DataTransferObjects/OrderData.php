<?php

declare(strict_types=1);

namespace Domain\Shop\Order\DataTransferObjects;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Enums\ClaimType;
use Domain\Shop\Order\Enums\PaymentMethod;
use Illuminate\Support\Carbon;

final readonly class OrderData
{
    public function __construct(
        public Branch $branch,
        public Customer $customer,
        public PaymentMethod $payment_method,
        public ClaimType $claimType,
        public Carbon $claim_at,
        public ?string $notes = null
    ) {
    }
}
