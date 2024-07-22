<?php

declare(strict_types=1);

use App\Settings\SiteSettings;
use Database\Support\SettingMigrationSupport;
use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    use SettingMigrationSupport;

    public function up(): void
    {
        $this->migrator->inGroup(SiteSettings::group(), function (SettingsBlueprint $blueprint): void {

            $blueprint->add('name', config('app.name'));
            $blueprint->add('favicon', $this->upload('favicon.svg', base_path('test_files/linux-sample-white.svg')));
            $blueprint->add('logo', $this->upload('logo.jpg', base_path('test_files/1-800x600.jpg')));
            $blueprint->add('address');

        });
    }
};
