<?php

return [
    'exec_sql' => [
        'driver'=> 'daily',
        'path'  => storage_path('logs/laravel-executed-sql.log'),
        'level' => 'debug',
        'days'  => 14,
        'permission' => '0666'
    ],

    'exec_redis' => [
        'driver'=> 'daily',
        'path'  => storage_path('logs/laravel-executed-redis.log'),
        'level' => 'debug',
        'days'  => 14,
        'permission' => '0666'
    ],

    'exec_request' => [
        'driver'=> 'daily',
        'path'  => storage_path('logs/laravel-executed-request.log'),
        'level' => 'debug',
        'days'  => 14,
        'permission' => '0666'
    ]
];
