<?php

declare(strict_types=1);

namespace Domain\Access\Role\DataTransferObjects;

final readonly class RoleData
{
    /** @param  array<int, int>  $permissions */
    public function __construct(
        public string $name,
        public string $guard_name,
        public array $permissions,
    ) {
    }
}
