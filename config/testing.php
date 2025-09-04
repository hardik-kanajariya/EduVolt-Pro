<?php

return [
    /*
    |--------------------------------------------------------------------------
    | EduVault Pro Testing Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options specific to testing
    | environment for EduVault Pro application.
    |
    */

    'database' => [
        'default' => 'sqlite',
        'connections' => [
            'sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ],
    ],

    'cache' => [
        'default' => 'array',
        'stores' => [
            'array' => [
                'driver' => 'array',
                'serialize' => false,
            ],
        ],
    ],

    'mail' => [
        'default' => 'array',
        'mailers' => [
            'array' => [
                'transport' => 'array',
            ],
        ],
    ],

    'queue' => [
        'default' => 'sync',
        'connections' => [
            'sync' => [
                'driver' => 'sync',
            ],
        ],
    ],

    'session' => [
        'driver' => 'array',
        'lifetime' => 120,
        'expire_on_close' => false,
        'encrypt' => false,
        'files' => storage_path('framework/sessions'),
        'connection' => null,
        'table' => 'sessions',
        'store' => null,
        'lottery' => [2, 100],
        'cookie' => env('SESSION_COOKIE', 'laravel_session'),
        'path' => '/',
        'domain' => env('SESSION_DOMAIN'),
        'secure' => env('SESSION_SECURE_COOKIE'),
        'http_only' => true,
        'same_site' => 'lax',
    ],

    'filesystem' => [
        'default' => 'local',
        'disks' => [
            'local' => [
                'driver' => 'local',
                'root' => storage_path('app'),
                'throw' => false,
            ],
            'public' => [
                'driver' => 'local',
                'root' => storage_path('app/public'),
                'url' => env('APP_URL') . '/storage',
                'visibility' => 'public',
                'throw' => false,
            ],
        ],
    ],

    'logging' => [
        'default' => 'single',
        'channels' => [
            'single' => [
                'driver' => 'single',
                'path' => storage_path('logs/testing.log'),
                'level' => env('LOG_LEVEL', 'debug'),
            ],
        ],
    ],

    'users' => [
        'admin' => [
            'name' => 'Test Admin',
            'email' => 'admin@eduvaultpro.com',
            'password' => 'Admin@123',
            'role' => 'admin',
        ],
        'teacher' => [
            'name' => 'Test Teacher',
            'email' => 'teacher@eduvaultpro.com',
            'password' => 'Teacher@123',
            'role' => 'teacher',
        ],
        'student' => [
            'name' => 'Test Student',
            'email' => 'student@eduvaultpro.com',
            'password' => 'Student@123',
            'role' => 'student',
        ],
    ],

    'api' => [
        'throttle' => [
            'enabled' => false,
            'max_attempts' => 1000,
            'decay_minutes' => 1,
        ],
    ],

    'features' => [
        'notifications' => false,
        'broadcasting' => false,
        'mail_sending' => false,
        'file_uploads' => true,
        'api_logging' => false,
    ],
];
