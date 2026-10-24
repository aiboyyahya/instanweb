<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
    ],
    'rajaongkir' => [
        'key' => env('RAJAONGKIR_API_KEY'),
        'base_url' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1/'),
        'auth_type' => env('RAJAONGKIR_AUTH_TYPE', 'header'),
        'auth_header' => env('RAJAONGKIR_AUTH_HEADER', 'key'),

        'endpoints' => [
            'provinces' => env('RAJAONGKIR_PROVINCES_ENDPOINT', 'destination/province'),
            'cities' => env('RAJAONGKIR_CITIES_ENDPOINT', 'destination/city'),
            'districts' => env('RAJAONGKIR_DISTRICTS_ENDPOINT', 'destination/district'),
            'cost' => env('RAJAONGKIR_COST_ENDPOINT', 'calculate/district/domestic-cost'),
            'search_city' => env('RAJAONGKIR_SEARCH_CITY_ENDPOINT', 'destination/city'), // pakai yang sama
        ],

        'origin_city_id' => env('RAJAONGKIR_ORIGIN_CITY_ID', 469),
        'origin_district_id' => env('RAJAONGKIR_ORIGIN_DISTRICT_ID'),
        'timeout' => env('RAJAONGKIR_TIMEOUT', 15),
    ],
    /*
    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        'options' => [],
        'finish_url' => env('MIDTRANS_FINISH_URL'),
        'unfinish_url' => env('MIDTRANS_UNFINISH_URL'),
        'error_url' => env('MIDTRANS_ERROR_URL'),
    ],
    */

];
