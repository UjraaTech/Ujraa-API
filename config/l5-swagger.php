<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Ujraa API Documentation',
                'version' => '1.0.0',
            ],
            'routes' => [
                'api' => 'api/documentation',
                 'docs' => 'docs',  // Remove the {json?} parameter
            ],
            'paths' => [
                'docs' => public_path('docs'),  // تأكد من أن المسار يشير إلى المجلد الصحيح
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'use_absolute_path' => false,
                'annotations' => [
                    base_path('app'),
                ],
            ],
            'proxy' => false,
            'operations_sort' => null,
            'validator_url' => null,
            'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', true),
            'additional_config_url' => null,
        ],
    ],
    'defaults' => [
        'routes' => [
            'docs' => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],
        ],
        'paths' => [
            'docs' => storage_path('api-docs'),
            'views' => resource_path('views/vendor/l5-swagger'),
            'base' => env('L5_SWAGGER_BASE_PATH', null),
            'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),
            'excludes' => [],
        ],
        'securityDefinitions' => [
            'securitySchemes' => [
                'bearer_token' => [
                    'type' => 'apiKey',
                    'name' => 'Authorization',
                    'in' => 'header'
                ]
            ]
        ],
        'security' => [
            ['bearer_token' => []]
        ],
        'ui' => [
            'authorization' => [
                'persist_authorization' => true
            ]
        ]
    ],
    'security' => [
        'additional_config_url' => null,
        'swagger_ui_middleware' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
        ],
    ],
];
