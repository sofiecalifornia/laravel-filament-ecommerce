<?php

declare(strict_types=1);

namespace App\Notifications;

use Exception;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ExportFinishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $fileName
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /** @throws Exception */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->success()
            ->title('Export finished')
            ->body($this->line())
            ->icon('heroicon-o-arrow-down-tray')
            ->actions([
                Action::make('download')
                    ->button()
                    ->markAsRead()
                    ->url($this->downloadUrl()),
            ])
            ->getDatabaseMessage();
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->greeting('Export finished')
            ->line($this->line())
            ->action('Download', $this->downloadUrl());
    }

    private function line(): string
    {

        return (string) Str::of('Your file [:value] is ready for download.')
            ->replace(':value', (string) Str::of($this->fileName)->substr(37));

    }

    private function downloadUrl(): string
    {
        return route('filament-excel-download', $this->fileName);
    }
}
