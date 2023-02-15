<?php

return [

    /**
     * @todo
     */
    'key' => env('CONFIGCAT_KEY', 'none'),

   /**
     * @todo
     */
    'log' => [
        'channel' => env('CONFIGCAT_LOG_CHANNEL', env('LOG_CHANNEL', 'stack')),
        'level' => env('CONFIGCAT_LOG_LEVEL', \ConfigCat\Log\LogLevel::WARNING),
    ],

    /**
     * @todo
     */
    'cache' => [
        'store' => env('CONFIGCAT_CACHE_DRIVER', env('CACHE_DRIVER', 'file')),
        'interval' => env('CONFIGCAT_CACHE_REFRESH_INTERVAL', 60),
    ],

    /**
     * @todo
     */
    'user' => function ($user) {
        // ...
    },

    /**
     * @todo
     */
    'overrides' => [
        'enabled' => env('CONFIGCAT_OVERRIDES_ENABLED', false),
        'file' => storage_path('app/features/configcat.json'),
    ],

];
