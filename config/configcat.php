<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ConfigCat SDK key
    |--------------------------------------------------------------------------
    |
    | SDK Key to access your feature flag and setting. Get it from ConfigCat
    | Dashboard. This is a hard requirement in order to use this package.
    | An invalid key like 'none' could be used when you are willing to
    | make the package "offline" and only use default values.
    */

    'key' => env('CONFIGCAT_KEY', 'none'),

    /*
    |--------------------------------------------------------------------------
    | Default value
    |--------------------------------------------------------------------------
    |
    | Here you can define the value which will be returned every time there
    | is a problem trying to reach for ConfigCat CDN or when the feature
    | flag you are trying to retrieve could not be found. This can be
    | overridden when using ConfigCat::get() or configcat() too.
    */

    'default' => false,

    /*
    |--------------------------------------------------------------------------
    | ConfigCat Logging
    |--------------------------------------------------------------------------
    |
    | ConfigCat SDK can log any operation its running in the background. It
    | is also compatible with Monolog. Here you can specify which one of
    | your application log channel you would like to use for such log
    | messages as well as the log level to use for the SDK. We took
    | care to set up some sensible defaults.
    |
    | See https://configcat.com/docs/sdk-reference/php/#logging
    */

    'log' => [

        'channel' => env('CONFIGCAT_LOG_CHANNEL', env('LOG_CHANNEL', 'stack')),

        'level' => env('CONFIGCAT_LOG_LEVEL', \ConfigCat\Log\LogLevel::WARNING),

    ],

    /*
    |--------------------------------------------------------------------------
    | ConfigCat Caching
    |--------------------------------------------------------------------------
    |
    | ConfigCat needs to cache the feature flag values retrieved from the CDN
    | in order to prevent doing too many HTTP calls. Here you can specify
    | which cache store defined within config/cache.php you'd like to
    | use as well as at which interval in seconds you would like it
    | to live for. We took care to set up some sensible defaults.
    |
    | See https://configcat.com/docs/sdk-reference/php/#cache
    */

    'cache' => [

        'store' => env('CONFIGCAT_CACHE_DRIVER', env('CACHE_DRIVER', 'file')),

        'interval' => env('CONFIGCAT_CACHE_REFRESH_INTERVAL', 60),

    ],

    /*
    |--------------------------------------------------------------------------
    | ConfigCat User Object
    |--------------------------------------------------------------------------
    |
    | This is an optional Transformer you can define to map some kind of user
    | representation you may have within your application into one that
    | ConfigCat will understand. Make sure to map and transform your
    | user before returning an instance of \ConfigCat\User::class
    |
    | If this is defined, and you don't pass a user representation explicitly
    | when resolving a feature flag with configcat() or ConfigCat::get(), we
    | will automatically use the logged-in user if found when trying to
    | resolve a feature flag value based on the logic you have set up
    | directly on ConfigCat Dashboard.
    |
    | Note: for security reason, no user information is transiting through HTTP
    | as the entire computation of the feature flag values are executed on
    | the server locally by ConfigCat SDK.
    |
    | See https://configcat.com/docs/sdk-reference/php/#user-object
    */

    // 'user' => \PodPoint\ConfigCat\Support\DefaultUserTransformer::class,

    /*
    |--------------------------------------------------------------------------
    | ConfigCat Flag Overrides
    |--------------------------------------------------------------------------
    |
    | As soon as enabled, this package will no longer reach for ConfigCat's CDN
    | and will instead use the file specified to retrieve any feature flag's
    | values. This can be useful in order to force some overrides at all
    | times. This can also be used in parallel with the facade method
    | ConfigCat::override() when willing to fake and control flags
    | at will within an end-to-end test case scenario.
    |
    | See https://configcat.com/docs/sdk-reference/php/#flag-overrides
    */

    'overrides' => [

        'enabled' => env('CONFIGCAT_OVERRIDES_ENABLED', false),

        'file' => storage_path('app/features/configcat.json'),

    ],

];
