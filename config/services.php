<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    ],

    'google' => [
        'calendar_id'  => env('GOOGLE_CALENDAR_ID', 'primary'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
    ],

    'initial_credentials' => [
        'admin_password' => env('INITIAL_ADMIN_PASSWORD'),
        'owner_password' => env('INITIAL_OWNER_PASSWORD'),
    ],

];
