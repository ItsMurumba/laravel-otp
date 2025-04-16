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
    'length' => 6,

    /*
    |--------------------------------------------------------------------------
    | Default OTP Expiration Time
    |--------------------------------------------------------------------------
    |
    | This value determines the default expiration time of OTPs in minutes.
    |
    */
    'expires_in' => 5,

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
            // Add your SMS provider configuration here
        ],
        'email' => [
            'driver' => 'email',
            // Add your email configuration here
        ],
        'slack' => [
            'driver' => 'slack',
            'webhook_url' => env('SLACK_WEBHOOK_URL'),
            'default_channel' => env('SLACK_DEFAULT_CHANNEL'),
            'message' => 'Your OTP code is: {otp}',
        ],
    ],
]; 