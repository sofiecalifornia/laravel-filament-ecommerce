<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => Sentry\Laravel\Features\Storage\Integration::configureDisks([ # https://github.com/getsentry/sentry-laravel/releases/tag/3.8.0

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        's3-db-backup' => [
            'driver' => 's3',
            'key' => env('AWS_DB_BACKUP_ACCESS_KEY_ID'),
            'secret' => env('AWS_DB_BACKUP_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DB_BACKUP_DEFAULT_REGION'),
            'bucket' => env('AWS_DB_BACKUP_BUCKET'),
            'url' => env('AWS_DB_BACKUP_URL'),
            'endpoint' => env('AWS_DB_BACKUP_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_DB_BACKUP_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        's3-private' => [
            'driver' => 's3',
            'key' => env('AWS_PRIVATE_ACCESS_KEY_ID'),
            'secret' => env('AWS_PRIVATE_SECRET_ACCESS_KEY'),
            'region' => env('AWS_PRIVATE_DEFAULT_REGION'),
            'bucket' => env('AWS_PRIVATE_BUCKET'),
            'url' => env('AWS_PRIVATE_URL'),
            'endpoint' => env('AWS_PRIVATE_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_PRIVATE_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],
    ]),

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
