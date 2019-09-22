<?php

return [
    'sql' => [
        'driver'=> 'daily',
        'path'  => storage_path('logs/laravel-executed.log'),
        'level' => 'debug',
        'days'  => 14,
    ]
];
