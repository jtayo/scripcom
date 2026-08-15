<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'tolclin' => [
        'base_url' => env('TOLCLIN_BASE_URL', 'https://api.tolclin.com'),
        'username' => env('TOLCLIN_USERNAME'),
        'password' => env('TOLCLIN_PASSWORD'),
        'router_ids' => env('TOLCLIN_ROUTER_IDS'),
        'organization_id' => env('TOLCLIN_ORGANIZATION_ID'),
        'ssid' => env('TOLCLIN_SSID'),
        'webhook_secret' => env('TOLCLIN_WEBHOOK_SECRET'),
        'grant_access' => env('TOLCLIN_GRANT_ACCESS', true),
        'keep_alive' => env('TOLCLIN_KEEP_ALIVE', false),
    ],

    'mpesa' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE'),
        'passkey' => env('MPESA_PASSKEY'),
        'environment' => env('MPESA_ENV', 'sandbox'),
    ],

];
