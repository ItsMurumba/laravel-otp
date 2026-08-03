# Changelog

All notable changes to `laravel-otp` will be documented in this file.

## Unreleased

### Added

- `TelegramChannel` implementation (was documented/tested but missing)
- Pest test suite now boots a real Laravel app via Orchestra Testbench
- Code coverage via `composer test:coverage` and CI (`.github/workflows/tests.yml`)

### Fixed

- Config and migration publish paths in `OtpServiceProvider` pointed at non-existent directories
- `otp()` helper resolved a non-existent container binding
- `composer.json` facade alias (`Mpesa` → `Otp`), leftover from a template
- `EmailChannel` now sends via a proper `Mailable` so it can be faked/asserted in tests
- `SmsChannel` was a no-op stub; now sends over HTTP like the other channels
- `NumericGenerator::validate()` didn't enforce a minimum length
- README examples described an API (`via()`, array-returning `generateAndSend()`, `validate()`) that didn't match the actual `OtpService`

### Removed

- Dead `src/Otp.php` stub class

## 1.0.0 - 2024-04-16

### Added

- Initial release
- Support for multiple OTP delivery channels:
  - Email
  - SMS
  - WhatsApp
  - Telegram
  - Slack
- Configurable OTP formats (numeric, alphanumeric)
- Customizable OTP length and expiration time
- Channel-specific validation
- Comprehensive test suite
- Detailed documentation
