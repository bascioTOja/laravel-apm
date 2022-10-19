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
];
