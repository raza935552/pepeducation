<?php

return [
    'site_id' => env('CUSTOMERIO_SITE_ID', ''),
    'api_key' => env('CUSTOMERIO_API_KEY', ''),
    'region' => env('CUSTOMERIO_REGION', 'us'),
    'base_urls' => [
        'us' => 'https://track.customer.io/api/v1/',
        'eu' => 'https://track-eu.customer.io/api/v1/',
    ],
    'timeout' => 30,
];
