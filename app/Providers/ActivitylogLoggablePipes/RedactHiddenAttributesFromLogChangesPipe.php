<?php

declare(strict_types=1);

namespace App\Providers\ActivitylogLoggablePipes;

use Closure;
use Spatie\Activitylog\Contracts\LoggablePipe;
use Spatie\Activitylog\EventLogBag;

class RedactHiddenAttributesFromLogChangesPipe implements LoggablePipe
{
    private const string REDACT_VALUE = '[*REDACTED*]';

    #[\Override]
    public function handle(EventLogBag $event, Closure $next): EventLogBag
    {
        $hiddenAttributes = $event->model->getHidden();

        if (0 === count($hiddenAttributes)) {
            return $next($event);
        }

        if (isset($event->changes['attributes'])) {
            $event->changes['attributes'] = self::redactValues($hiddenAttributes, $event->changes['attributes']);
        }

        if (isset($event->changes['old'])) {
            $event->changes['old'] = self::redactValues($hiddenAttributes, $event->changes['old']);
        }

        return $next($event);
    }

    private static function redactValues(array $hiddenAttributes, array $changes): array
    {
        foreach ($changes as $property => $value) {
            if (in_array($property, $hiddenAttributes)) {
                $changes[$property] = self::REDACT_VALUE;
            }
        }

        return $changes;
    }
}
