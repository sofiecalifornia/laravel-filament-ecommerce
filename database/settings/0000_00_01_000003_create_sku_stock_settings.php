<?php

declare(strict_types=1);

use App\Settings\SkuStockSettings;
use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup(SkuStockSettings::group(), function (SettingsBlueprint $blueprint): void {

            $blueprint->add('yellow_warning_count', 10);
            $blueprint->add('red_warning_count', 5);

        });
    }
};
