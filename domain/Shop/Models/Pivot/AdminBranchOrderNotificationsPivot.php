<?php

declare(strict_types=1);

namespace Domain\Shop\Models\Pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Domain\Shop\Models\Pivot\AdminBranch
 *
 * @property int $id
 * @property string $admin_uuid
 * @property string $branch_uuid
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Models\Pivot\AdminBranchOrderNotificationsPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Models\Pivot\AdminBranchOrderNotificationsPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Models\Pivot\AdminBranchOrderNotificationsPivot query()
 *
 * @mixin \Eloquent
 */
class AdminBranchOrderNotificationsPivot extends Pivot
{
    protected $table = 'admin_branch_order_notifications';
}
