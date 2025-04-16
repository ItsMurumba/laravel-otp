# Laravel OTP

A flexible and feature-rich Laravel package for generating and sending One-Time Passwords (OTP) through multiple channels including Email, SMS, WhatsApp, Telegram, and Slack.

## Features

- 📱 Multiple delivery channels (Email, SMS, WhatsApp, Telegram, Slack)
- 🔢 Configurable OTP formats (numeric, alphanumeric)
- ⏱️ Customizable expiration time
- 🔄 Easy channel switching
- ✅ Channel-specific validation
- 🛠️ Extensible architecture

## Requirements

- PHP 8.1 or higher
- Laravel 10.0 or higher

## Installation

You can install the package via composer:

```bash
composer require itsmurumba/laravel-otp
```

After installation, publish the configuration file:

```bash
php artisan vendor:publish --provider="Itsmurumba\Otp\OtpServiceProvider"
```

## Environment Variables

The following environment variables need to be configured in your `.env` file:

### OTP General Configuration

```env
# Default channel for sending OTP (email, sms, whatsapp, telegram, slack)
OTP_DEFAULT_CHANNEL=email

# OTP length (number of characters)
OTP_LENGTH=6

# OTP expiration time in minutes
OTP_EXPIRES_IN=10

# OTP type (numeric or alphanumeric)
OTP_TYPE=numeric
```

### Email Configuration

```env
# Laravel mail settings
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=from@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### SMS Configuration

```env
# SMS API credentials
SMS_API_KEY=your-api-key
SMS_API_URL=https://your-sms-provider.com/api
```

### WhatsApp Configuration

```env
# WhatsApp Business API credentials
WHATSAPP_API_KEY=your-api-key
WHATSAPP_API_URL=https://your-whatsapp-provider.com/api
```

### Telegram Configuration

```env
# Telegram Bot Token
TELEGRAM_BOT_TOKEN=your-bot-token
```

### Slack Configuration

```env
# Slack Webhook URL
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/your-webhook-url
```

## Configuration

The package configuration file will be published at `config/otp.php`. Here you can configure:

```php
return [
    // Default channel for sending OTP
    'default_channel' => env('OTP_DEFAULT_CHANNEL', 'email'),

    // OTP length
    'length' => env('OTP_LENGTH', 6),

    // OTP expiration time in minutes
    'expires_in' => env('OTP_EXPIRES_IN', 10),

    // OTP type (numeric or alphanumeric)
    'type' => env('OTP_TYPE', 'numeric'),

    // Channel configurations
    'channels' => [
        'email' => [
            'enabled' => true,
            'from' => env('MAIL_FROM_ADDRESS'),
            'name' => env('MAIL_FROM_NAME'),
        ],
        'sms' => [
            'enabled' => true,
            'api_key' => env('SMS_API_KEY'),
            'api_url' => env('SMS_API_URL'),
        ],
        'whatsapp' => [
            'enabled' => true,
            'api_key' => env('WHATSAPP_API_KEY'),
            'api_url' => env('WHATSAPP_API_URL'),
        ],
        'telegram' => [
            'enabled' => true,
            'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        ],
        'slack' => [
            'enabled' => true,
            'webhook_url' => env('SLACK_WEBHOOK_URL'),
        ],
    ],
];
```

## Usage

### Basic Usage

```php
use Itsmurumba\Otp\Services\OtpService;

// Create an instance of OtpService
$otpService = new OtpService();

// Generate and send OTP
$result = $otpService->generateAndSend('recipient@example.com');

if ($result) {
    $otp = $result['otp'];
    $expiresAt = $result['expires_at'];
    // Store or handle the OTP as needed
}
```

### Channel-Specific Usage

#### Email

```php
use Itsmurumba\Otp\Channels\EmailChannel;

$channel = new EmailChannel();
$result = $channel->send('user@example.com', '123456');
```

#### SMS

```php
use Itsmurumba\Otp\Channels\SmsChannel;

$channel = new SmsChannel();
$result = $channel->send('+1234567890', '123456');
```

#### WhatsApp

```php
use Itsmurumba\Otp\Channels\WhatsAppChannel;

$channel = new WhatsAppChannel();
$result = $channel->send('+1234567890', '123456');
```

#### Telegram

```php
use Itsmurumba\Otp\Channels\TelegramChannel;

$channel = new TelegramChannel();
$result = $channel->send('chat_id', '123456');
```

#### Slack

```php
use Itsmurumba\Otp\Channels\SlackChannel;

$channel = new SlackChannel();
$result = $channel->send('#channel-name', '123456');
```

### Custom Channel Configuration

You can specify a different channel at runtime:

```php
$otpService = new OtpService();
$otpService->via('sms')->generateAndSend('+1234567890');
```

### OTP Validation

```php
$otpService = new OtpService();
$isValid = $otpService->validate('123456', 'stored_otp', $expirationTimestamp);
```

## Channel-Specific Requirements

### Email

- Configured Laravel mail settings in `.env`

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=from@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### SMS

- Valid SMS API credentials

```env
SMS_API_KEY=your-api-key
SMS_API_URL=https://your-sms-provider.com/api
```

### WhatsApp

- Valid WhatsApp Business API credentials

```env
WHATSAPP_API_KEY=your-api-key
WHATSAPP_API_URL=https://your-whatsapp-provider.com/api
```

### Telegram

- Valid Telegram Bot Token

```env
TELEGRAM_BOT_TOKEN=your-bot-token
```

### Slack

- Valid Slack Webhook URL

```env
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/your-webhook-url
```

## Testing

```bash
composer test
```

## Security

If you discover any security-related issues, please email kevmurumba@gmail.com instead of using the issue tracker.

## Credits

- [Kelvin Murumba](https://github.com/itsmurumba)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
