<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Support\Facades\Http;
use Sentry\SentrySdk;
use Spatie\SupportBubble\Events\SupportBubbleSubmittedEvent;

class SupportBubbleSubmittedToSentryFeedbackListener
{
    public function handle(SupportBubbleSubmittedEvent $event): void
    {
        $dns = config('sentry.dsn');

        if (blank($dns)) {
            return;
        }

        $organizationSlug = config('app-sentry.organization_slug');
        $projectSlug = config('app-sentry.project_slug');

        Http::withToken($dns, type: 'DSN')
            ->asJson()
            ->post(
                "https://sentry.io/api/0/projects/$organizationSlug/$projectSlug/user-feedback/",
                data: [
                    'event_id' => SentrySdk::getCurrentHub()->getLastEventId(),
                    'name' => $event->name,
                    'email' => $event->email,
                    'comments' => $event->subject.': '.$event->message,
                ]);
    }
}
