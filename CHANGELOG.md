# Changelog

All notable changes to `laravel-otp` will be documented in this file.

## Unreleased

### Added

- `TelegramChannel` implementation (was documented/tested but missing)
- Pest test suite now boots a real Laravel app via Orchestra Testbench
- Code coverage via `composer test:coverage` and CI (`.github/workflows/tests.yml`)
- `verify()` is now rate-limited independently from OTP generation, guarding against brute-forcing a code before it expires
- CI now matrixes Laravel 10 and 11 as well as 12 and 13

### Changed

- OTP codes are now hashed (SHA-256) before being stored; `generateAndSend()` still returns the plaintext code to the caller
- `composer.json`'s `php`/`illuminate/support` constraints narrowed to versions that are actually CI-tested (PHP 8.1+, Laravel 10-13) — the previous range (PHP 7.1+, Laravel 5+) couldn't run this code at all (it uses PHP 8.0-only syntax)

### Fixed

- Config and migration publish paths in `OtpServiceProvider` pointed at non-existent directories
- `otp()` helper resolved a non-existent container binding
- `composer.json` facade alias (`Mpesa` → `Otp`), leftover from a template
- `EmailChannel` now sends via a proper `Mailable` so it can be faked/asserted in tests
- `SmsChannel` was a no-op stub; now sends over HTTP like the other channels
- `NumericGenerator::validate()` didn't enforce a minimum length
- README examples described an API (`via()`, array-returning `generateAndSend()`, `validate()`) that didn't match the actual `OtpService`
- `config/otp.php`'s `length`, `expires_in`, `default_channels`, and `rate_limit` were declared but never read by `OtpServiceProvider` — editing them had no effect
- `.env.example` documented `OTP_DEFAULT_CHANNEL`/`OTP_LENGTH`/`OTP_EXPIRES_IN`/`OTP_TYPE`, none of which mapped to a real config key; now `OTP_LENGTH`/`OTP_EXPIRES_IN`/`OTP_GENERATOR` are wired into `config/otp.php`
- `NumericGenerator`/`AlphanumericGenerator::validate()` accepted any code of length 6 or more instead of checking against the actual expected length

### Removed

- Dead `src/Otp.php` stub class
- Dead `src/Exceptions/IsNullException.php` (never thrown or caught)

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
