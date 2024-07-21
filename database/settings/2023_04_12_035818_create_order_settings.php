<?php

declare(strict_types=1);

use App\Settings\OrderSettings;
use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup(OrderSettings::group(), function (SettingsBlueprint $blueprint): void {

            $blueprint->add('prefix', 'ORDER');

        });
    }
};
