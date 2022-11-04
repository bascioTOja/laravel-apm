<?php

return [
    'enabled'          => env('APM', false),
    'request_enabled'  => true,
    'query_enabled'    => true,
    'schedule_enabled' => true,
    'job_enabled'      => true,

    /*
     * Register only paths starting with
     */
    'starts_with' => [
    ],

    /*
     * Excluded paths from register
     */
    'excluded' => [
        'apm',
    ],

    'route' => [
        'uri'  => '/apm',
        'name' => 'apm',
    ],
    'middlewares' => [
        'web',
    ],

    /*
     * How long files will be kept,
     * apm:clear command will delete files older than the number of days
     */
    'keep_for_days' => 7,
];
