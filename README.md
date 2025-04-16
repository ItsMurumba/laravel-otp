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

## Configuration

Copy the required environment variables from `.env.example` to your `.env` file and configure them according to your needs:

```bash
cp .env.example .env
```

The package supports the following channels, each requiring specific configurations:

#### Email

- Requires Laravel mail settings
- Uses standard Laravel mail configuration

#### SMS

- Requires SMS API credentials
- Configure `SMS_API_KEY` and `SMS_API_URL`

#### WhatsApp

- Requires WhatsApp Business API credentials
- Configure `WHATSAPP_API_KEY` and `WHATSAPP_API_URL`

#### Telegram

- Requires Telegram Bot Token
- Configure `TELEGRAM_BOT_TOKEN`

#### Slack

- Requires Slack Webhook URL
- Configure `SLACK_WEBHOOK_URL`

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
