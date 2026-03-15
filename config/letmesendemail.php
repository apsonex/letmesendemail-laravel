<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    | Your LetMeSendEmail API key. Set this in your `.env` file as LMSE_API_KEY.
    */
    'key' => env('LMSE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default Domain
    |--------------------------------------------------------------------------
    | Optionally specify the default sending domain.
    */
    'domain' => env('LMSE_DOMAIN_NAME'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Route
    |--------------------------------------------------------------------------
    | The path where webhook requests are received.
    */
    'route' => [
        'path' => env('LMSE_ROUTE_PATH', 'let-me-send-email'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    | Secret and tolerance settings for verifying incoming webhooks.
    */
    'webhook' => [
        'enable' => env('LMSE_WEBHOOK_ENABLE', true),
        'secret' => env('LMSE_WEBHOOK_SECRET'),
        'tolerance' => env('LMSE_WEBHOOK_TOLERANCE', 300), // in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Request Client Options
    |--------------------------------------------------------------------------
    */
    'client' => [
        'options' => [],
    ],
];
