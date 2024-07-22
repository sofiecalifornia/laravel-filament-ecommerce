<?php

declare(strict_types=1);

namespace App\Settings;

use Domain\Access\Admin\Models\Admin;
use Illuminate\Database\Eloquent\Collection;

class OrderSettings extends BaseSettings
{
    public string $prefix;

    /**
     * @var array<int, int>
     */
    public array $admin_notification_ids;

    public int $maximum_advance_booking_days;

    #[\Override]
    public static function group(): string
    {
        return 'order';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Admin\Models\Admin>
     */
    public function getAdminNotifications(): Collection
    {
        return Admin::whereKey($this->admin_notification_ids)->get();
    }
}
