<?php

return [
    /*
     *  Automatic registration of routes will only happen if this setting is `true`
     */
    'enabled' => true,

    /*
     * Controllers in these directories that have routing attributes
     * will automatically be registered.
     *
     * Optionally, you can specify group configuration by using key/values
     */
    'directories' => [
//        app_path('Http/Controllers/Frontend') => [
//            'as' => 'frontend.',
//            'middleware' =>['web'],
//        ],
        app_path('Http/Controllers/Admin') => [
            'as' => 'admin.',
            'prefix' => 'admin',
            'middleware' =>['web', 'auth:admin'],
        ],
        app_path('Http/Controllers/API') => [
            'as' => 'api.',
            'prefix' => 'api',
            'middleware' => 'api',
        ],
    ],

    /**
     * This middleware will be applied to all routes.
     */
    'middleware' => [
        \Illuminate\Routing\Middleware\SubstituteBindings::class
    ]
];
