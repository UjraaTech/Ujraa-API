<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Swagger documentation route
Route::get('/api/documentation', function () {
    return view('vendor.l5-swagger.index', [
        'documentation' => 'default',
        'versions' => [
            'default' => config('l5-swagger.defaults', [])
        ],
        'urlToDocs' => url('/docs/api-docs.json'),  // تحديد المسار الكامل مع الشرطة
        'urls' => [
            [
                'url' => '/docs/api-docs.json',  // استخدام المسار المباشر مع الشرطة
                'name' => 'default'
            ]
        ],
        'useAbsolutePath' => true,
        'operationsSorter' => null,
        'configUrl' => null,
        'validatorUrl' => null,
        'oauth2RedirectUrl' => null,
        'middlewares' => [],
        'securityDefinitions' => [
            'securitySchemes' => [
                'bearer_token' => [
                    'type' => 'apiKey',
                    'name' => 'Authorization',
                    'in' => 'header'
                ]
            ]
        ]
    ]);
})->name('l5-swagger.default.api');

// ... existing code ...