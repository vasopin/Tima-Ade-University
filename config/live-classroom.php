<?php

return [
    'provider' => env('LIVE_VIDEO_PROVIDER', 'mock'),
    'production' => [
        'api_url' => env('LIVE_VIDEO_API_URL'),
        'api_key' => env('LIVE_VIDEO_API_KEY'),
        'api_secret' => env('LIVE_VIDEO_API_SECRET'),
        'app_id' => env('LIVE_VIDEO_APP_ID'),
    ],
    'development' => [
        'enabled' => env('LIVE_VIDEO_DEVELOPMENT_MODE', true),
    ],
];
