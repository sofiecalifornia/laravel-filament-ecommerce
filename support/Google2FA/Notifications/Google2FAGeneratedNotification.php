<?php

declare(strict_types=1);

namespace Support\Google2FA\Notifications;

use Domain\Access\Admin\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class Google2FAGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $fileUrl)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(Admin $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(Admin $notifiable): MailMessage
    {
        return (new MailMessage())
            ->line(trans('Google2FA Secret Key Generated'))
            ->line(new HtmlString('<img src="'.$this->fileUrl.'" />'))
            ->line(trans('Your secret key is: :key', ['key' => $notifiable->google2fa_secret]));
    }
}
