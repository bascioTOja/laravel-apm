<?php

return [
    'enabled' => env('APM', false),
    'excluded' => [
        'apm'
    ],
    'route' => [
        'uri' => '/apm',
        'name' => 'apm'
    ],
    'middlewares' => [
        'web'
    ]
];
