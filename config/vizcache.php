<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    |
    | Provide configuration for your stats. Wildcard notation is supported.
    | Note that stat mathcing configurations will be applied in order of key
    | length (from shortest to longest). Also '.' separator must be used to
    | separate analyst and method names.
    |
    */

    'configuration' => [
        // Global configuration for all stats.
        '*' => [
            // Cache store to be used for caching. If set to false it will disable caching.
            'cache_store' => env('CACHE_DRIVER', 'file'),

            // Here you can specify global time to live in minutes for cached stats.
            // If set to 0 or false then stat values will be cached forever.
            'time_to_live' => 10,

            // If set true then stats will return only already cached values. If
            // cached value doesn't exist yet it will not be cached and default
            // value will be returned - the only way to cache value of such a stat
            // is calling it's update() and touch() methods explictily or via
            // schedule updating.
            //
            // Note that compute() method will still be available for such stat
            // if you need value directly from it's analyst.
            'only_get_from_cache' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Analysts
    |--------------------------------------------------------------------------
    |
    | Here you may specify analysts defined in your app. An analyst class
    | must be an instance of D3jn\Vizcache\Analyst and implement methods
    | for calculating your stat values.
    |
    */

    'analysts' => [
    ],

    /*
    |--------------------------------------------------------------------------
    | No Caching When Testing
    |--------------------------------------------------------------------------
    |
    | If set true then stats won't be cached when in testing environment.
    |
    */

    'no_caching_when_testing' => false,

];
