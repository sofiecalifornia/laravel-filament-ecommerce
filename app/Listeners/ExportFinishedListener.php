<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Notifications\ExportFinishedNotification;
use Domain\Access\Admin\Models\Admin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use pxlrbt\FilamentExcel\Events\ExportFinishedEvent;

class ExportFinishedListener implements ShouldQueue
{
    public function handle(ExportFinishedEvent $event): void
    {
        Notification::send(
            Admin::whereKey($event->userId)->first(),
            new ExportFinishedNotification(fileName: $event->filename)
        );
    }
}
