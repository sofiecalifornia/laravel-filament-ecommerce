<?php

declare(strict_types=1);

namespace Domain\Access\Role\Observers;

use App\Observers\LogAttemptDeleteResource;
use Domain\Access\Role\Models\Role;
use Filament\Support\Exceptions\Halt;

class RoleObserver
{
    use LogAttemptDeleteResource;

    /**
     * @throws Halt
     */
    public function deleting(Role $attribute): void
    {
        $attribute->loadCount('users');

        if ($attribute->users_count > 0) {

            self::abortThenLogAttemptDeleteRelationCount(
                $attribute,
                trans('Can not delete attribute with associated users.'),
                'attributeOptions',
                $attribute->users_count
            );

        }
    }
}
