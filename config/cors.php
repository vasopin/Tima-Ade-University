<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | This file overrides the default framework CORS settings to allow the
    | development frontend origin and enable credentialed requests required
    | by Laravel Sanctum (cookies + XSRF token).
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    // Allow front-end origin(s). Use comma-separated CORS_ALLOWED_ORIGINS in .env for flexibility.
    'allowed_origins' => array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000')))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Must be true for Sanctum to work with cookies (credentialed requests)
    'supports_credentials' => true,
];
