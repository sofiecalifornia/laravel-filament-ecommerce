<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Access\RoleResource\Support;

use Illuminate\Support\Str;

final readonly class PermissionData
{
    public bool $is_parent;

    public string $parent_name;

    public ?string $child_name;

    public function __construct(
        public string $uuid,
        public string $name,
    ) {
        $this->is_parent = ! Str::of($this->name)->contains('.');

        $this->parent_name = explode('.', $this->name)[0];

        if ($this->is_parent) {
            $this->child_name = null;
        } else {
            $this->child_name = explode('.', $this->name)[1];
        }
    }
}
