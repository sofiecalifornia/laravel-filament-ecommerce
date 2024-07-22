<?php

declare(strict_types=1);

return [
    'organization_slug' => env('APP__SENTRY_ORG_SLUG'),
    'project_slug' => env('APP__SENTRY_PROJECT_SLUG'),
    'session_replay' => env('APP__SENTRY_SESSION_REPLAY'),
];
