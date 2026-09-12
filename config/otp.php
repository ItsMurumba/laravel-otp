<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default OTP Length
    |--------------------------------------------------------------------------
    |
    | This value determines the default length of generated OTPs.
    |
    */
    'length' => env('OTP_LENGTH', 6),

    /*
    |--------------------------------------------------------------------------
    | Default OTP Expiration Time
    |--------------------------------------------------------------------------
    |
    | This value determines the default expiration time of OTPs in minutes.
    |
    */
    'expires_in' => env('OTP_EXPIRES_IN', 5),

    /*
    |--------------------------------------------------------------------------
    | Default Generator
    |--------------------------------------------------------------------------
    |
    | This value determines the default generator used for OTPs.
    | Options: numeric, alphanumeric
    |
    */
    'generator' => env('OTP_GENERATOR', 'numeric'),

    /*
    |--------------------------------------------------------------------------
    | Default Channels
    |--------------------------------------------------------------------------
    |
    | This value determines the default channels used for sending OTPs.
    |
    */
    'default_channels' => ['sms'],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for OTP requests.
    |
    */
    'rate_limit' => [
        'max_attempts' => 3,
        'decay_minutes' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Channel Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for each channel.
    |
    */
    'channels' => [
        'sms' => [
            'driver' => 'sms',
            'api_key' => env('SMS_API_KEY'),
            'api_url' => env('SMS_API_URL'),
        ],
        'email' => [
            'driver' => 'email',
            // Add your email configuration here
        ],
        'slack' => [
            'driver' => 'slack',
            'webhook_url' => env('SLACK_WEBHOOK_URL'),
            'message' => 'Your OTP code is: {otp}',
        ],
        'whatsapp' => [
            'driver' => 'whatsapp',
            'api_key' => env('WHATSAPP_API_KEY'),
            'api_url' => env('WHATSAPP_API_URL'),
            'message' => 'Your OTP code is: {otp}',
        ],
        'telegram' => [
            'driver' => 'telegram',
            'bot_token' => env('TELEGRAM_BOT_TOKEN'),
            'message' => 'Your OTP code is: {otp}',
        ],
    ],
]; 