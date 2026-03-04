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

    'glpi' => [
        'url' => env('GLPI_URL'),
        'app_token' => env('GLPI_APP_TOKEN'),
        'user_token' => env('GLPI_USER_TOKEN'),
    ],

    'microsoft_graph' => [
        'tenant_id' => env('MICROSOFT_TENANT_ID'),
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'mailbox' => env('MICROSOFT_CALENDAR_MAILBOX', 'ticket@salud.mdonihue.cl'),
        'timezone' => env('MICROSOFT_CALENDAR_TIMEZONE', 'UTC'),
    ],

    'microsoft' => [
        'client_id' => env('MICROSOFT_OAUTH_CLIENT_ID', env('MICROSOFT_CLIENT_ID')),
        'client_secret' => env('MICROSOFT_OAUTH_CLIENT_SECRET', env('MICROSOFT_CLIENT_SECRET')),
        'redirect' => env('MICROSOFT_OAUTH_REDIRECT_URI'),
        'tenant' => env('MICROSOFT_OAUTH_TENANT', 'organizations'),
    ],


];
