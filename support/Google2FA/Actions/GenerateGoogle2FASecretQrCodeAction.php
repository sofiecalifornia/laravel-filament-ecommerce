<?php

declare(strict_types=1);

namespace Support\Google2FA\Actions;

use App\Settings\SiteSettings;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Domain\Access\Admin\Models\Admin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

final readonly class GenerateGoogle2FASecretQrCodeAction
{
    private const string FOLDER = '2fa-qr-codes';

    private static function disk(): string
    {
        // TODO: 's3-private' to resolve file security
        return config('filesystems.default');
    }

    public function execute(Admin $admin): string
    {
        $path = self::FOLDER.'/admins/'.md5($admin->uuid.Str::random()).'.png';

        $qrCodeUrl = (new Google2FA())->getQRCodeUrl(
            app(SiteSettings::class)->name,
            $admin->email,
            $admin->google2fa_secret
        );

        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(400),
                new ImagickImageBackEnd()
            )
        );

        Storage::disk(self::disk())
            ->put($path, $writer->writeString($qrCodeUrl));

        return Storage::disk(self::disk())
            ->temporaryUrl($path, now()->addDay());
    }
}
