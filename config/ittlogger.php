<?php

return [
    'batch' => false,
    'queue' => [
        'enable' => env('LOG_QUEUE_ENABLED',false), 
        'name' => env('LOG_QUEUE_NAME', 'logging')
    ],
    'level' => 200,
    'bubble' => false,
    'opensearch' => [
        'hosts' => [
            [
                'host' => env('OPENSEARCH_HOST', 'localhost'),
                'port' => env('OPENSEARCH_PORT', 9200),
                'scheme' => env('OPENSEARCH_SCHEME', 'http'),
                'user' => env('OPENSEARCH_USER', null),
                'pass' => env('OPENSEARCH_PASS', null)
            ],
        ],
        'retries' => 2,
        'cert' => '',
        'params' => [
            'client' => [
                'timeout' => 2,
                'connect_timeout' => 2
            ]
        ]
    ],

    'options' => [
        'index' => strtolower(env('APP_NAME', 'laravel')),
        'type' => '_doc',
        'ignore_error' => false,
    ],
    'exception' => [
        'trace' => false,
    ],
    'extra' => [
        'host' => env('APP_URL'),
        'php' => PHP_VERSION,
        'laravel' => app()->version(),
        'env' => env('APP_ENV')
    ],
];
