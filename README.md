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

- PHP 8.2 or higher
- Laravel 12 or 13 (CI is tested against both). Laravel 10 and 11 aren't supported: both have an open, unpatched CVE ([CVE-2026-48019](https://packagist.org/advisories/PKSA-mdq4-51ck-6kdq)) with no fix released for those majors.

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

#### General

- `OTP_LENGTH` — length of generated codes (default: `6`)
- `OTP_EXPIRES_IN` — expiration time in minutes (default: `5`)
- `OTP_GENERATOR` — `numeric` or `alphanumeric` (default: `numeric`)

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
use Itsmurumba\Otp\Facades\Otp;

// Generate and send an OTP (defaults to config('otp.default_channels'), "sms" out of the box)
$otp = Otp::generateAndSend('recipient@example.com', 'email');

// Verify an OTP
$isValid = Otp::verify('recipient@example.com', $otp);
```

You can also resolve the underlying service directly instead of the facade (via the `otp()` helper or the `otp` container binding):

```php
$otpService = otp(); // same as app('otp')

$otp = $otpService->length(6)
    ->expiresIn(10)
    ->rateLimit(3, 15)
    ->generateAndSend('recipient@example.com', 'email');

$isValid = $otpService->verify('recipient@example.com', $otp);
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

### Sending via Multiple Channels

Pass an array of channel names to send the same OTP through more than one channel:

```php
$otp = $otpService->generateAndSend('+1234567890', ['sms', 'whatsapp']);
```

### OTP Verification

```php
$isValid = $otpService->verify('+1234567890', $otp);
```

`verify()` checks the code against the stored, non-expired, unverified OTP for that identifier and marks it as verified on success. Verification attempts are rate-limited independently from generation (same `rateLimit()`/`rate_limit` config), throwing `RateLimitExceededException` once the limit is hit — this stops brute-forcing a code before it expires.

## Testing

```bash
composer test
```

To run the test suite with code coverage (requires Xdebug or PCOV):

```bash
composer test:coverage
```

## Security

OTP codes are hashed (SHA-256) before being stored, so a database read or leaked backup doesn't expose usable codes. Generated codes are only ever returned to your application in plaintext, to send via a channel.

If you discover any security-related issues, please email kevmurumba@gmail.com instead of using the issue tracker.

## Credits

- [Kelvin Murumba](https://github.com/itsmurumba)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
