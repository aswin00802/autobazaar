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
        'client_id' => null,
        'client_secret' => null,
        'redirect' => null,
    ],
    'linkedin' => [
        'client_id' => null,
        'client_secret' => null,
        'redirect' => null,
    ],
    'facebook' => [
        'client_id' => null,
        'client_secret' => null,
        'redirect' => null,
    ],
    'github' => [
        'client_id' => null,
        'client_secret' => null,
        'redirect' => null,
    ],
    'twitter' => [
        'client_id' => null,
        'client_secret' => null,
        'redirect' => null,
    ],

    'razorpay' => [
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
    ],

    // SMS gateway used for every OTP the site and both apps send.
    // The key lives in .env (PING4SMS_KEY) and never in the repository.
    // If it is missing, no OTP can be sent, so the deploy checklist calls it out.
    'ping4sms' => [
        'key' => env('PING4SMS_KEY'),
        'url' => env('PING4SMS_URL', 'http://site.ping4sms.com/api/smsapi'),
    ],

];
