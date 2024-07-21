<?php

declare(strict_types=1);

namespace Domain\Shop\Customer\DataTransferObjects;

use Domain\Shop\Customer\Enums\Gender;

final readonly class CustomerData
{
    public function __construct(
        public string $email,
        public string $first_name,
        public string $last_name,
        public ?string $mobile,
        public Gender $gender,
        public ?string $password = null,
    ) {
    }
}
