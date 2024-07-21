<?php

declare(strict_types=1);

namespace App\Policies;

use App\Filament\Support\TenantHelper;
use Domain\Access\Role\ChecksWildcardPermissions;
use Illuminate\Foundation\Auth\User;
use Spatie\Activitylog\Models\Activity;

class ActivityPolicy
{
    use ChecksWildcardPermissions;

    public function before(?User $user, string $ability, mixed $activity = null): ?bool
    {
        if (TenantHelper::getBranch() !== null) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $this->checkWildcardPermissions($user);
    }

    public function view(User $user, Activity $activity): bool
    {
        return $this->checkWildcardPermissions($user);
    }
}
