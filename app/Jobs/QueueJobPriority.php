<?php

declare(strict_types=1);

namespace App\Jobs;

class QueueJobPriority
{
    final public const array PRIORITIES = [
        self::HIGH,
        self::MEDIUM,
        self::LOW,
        self::DEFAULT,
        self::EXCEL,
        self::MEDIA_LIBRARY,
        self::DB_BACKUP,
    ];

    final public const string HIGH = 'high';

    final public const string MEDIUM = 'medium';

    final public const string LOW = 'low';

    final public const string DEFAULT = 'default';

    final public const string EXCEL = 'excel';

    final public const string MEDIA_LIBRARY = 'media_library';

    final public const string DB_BACKUP = 'db_backup';

    private function __construct()
    {
        //
    }
}
