<?php

declare(strict_types=1);

namespace App\Settings;

class SiteSettings extends BaseSettings
{
    public string $name;

    public string $favicon;

    public string $logo;

    public ?string $address = null;

    #[\Override]
    public static function group(): string
    {
        return 'site';
    }

    public function getSiteFaviconUrl(): string
    {
        return $this->getUrlFromStorage($this->favicon);
    }

    public function getSiteLogoUrl(): string
    {
        return $this->getUrlFromStorage($this->logo);
    }
}
