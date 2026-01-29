<?php

declare(strict_types=1);

/**
 * Application Configuration
 * 
 * General application settings.
 */

return [
    'name'        => 'Headless E-Commerce API',
    'environment' => env('APP_ENV', 'production'),
    'debug'       => env('APP_DEBUG', false),
    'url'         => env('APP_URL', 'http://localhost'),
    
    'graphql' => [
        'debug'    => env('GRAPHQL_DEBUG', false),
        'endpoint' => '/graphql',
    ],
];