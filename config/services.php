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

    'fire_prediction' => [
        'url' => env('FIRE_PREDICTION_API_URL', ''),
    ],

    'nasa_firms' => [
        'api_key' => env('NASA_FIRMS_API_KEY', ''),
        'api_base' => env('NASA_FIRMS_API_BASE', 'https://firms.modaps.eosdis.nasa.gov/api/area/csv'),
    ],

    'hotspots_integration' => [
        'api_url' => env('HOTSPOTS_INTEGRATION_API_URL', 'http://gatealas.dasalas.shop/api/gateway/brigadas/hotspots'),
    ],

    'open_meteo' => [
        'api_url' => env('OPEN_METEO_API_URL', 'https://api.open-meteo.com/v1/forecast'),
    ],

    'sipi_weather' => [
        'api_url' => env('SIPI_WEATHER_API_URL', 'http://gatealas.dasalas.shop/api/gateway/prediccion/weather'),
    ],

    'external_fire_reports' => [
        'api_url' => env('EXTERNAL_FIRE_REPORTS_API_URL', 'http://gatealas.dasalas.shop/api/gateway/brigadas/reportesanimales'),
    ],

    'fire_predictions_lookup' => [
        'api_url' => env('FIRE_PREDICTIONS_LOOKUP_API_URL', 'http://gatealas.dasalas.shop/api/gateway/prediccion/lookup'),
    ],

];
