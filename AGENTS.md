# AGENTS.md

Guidance for AI-assisted development on this package.

## What this is

A Laravel package (`itsmurumba/laravel-otp`) that generates and sends OTPs through pluggable channels (email, SMS, WhatsApp, Telegram, Slack). Not an application — there's no `app/`, only a `src/` library consumed by a host Laravel app via `OtpServiceProvider`.

## Architecture

- `Services/OtpService` — the orchestrator. Generates a code via a `GeneratorInterface`, stores it in the `otps` table (`Models/Otp`), dispatches it to one or more registered `ChannelInterface` implementations, and enforces rate limiting via `Services/RateLimiter`.
- `Contracts/ChannelInterface` — `send(string $recipient, string $otp, array $data = []): bool` + `getName(): string`. Every delivery channel implements this.
- `Contracts/GeneratorInterface` — `generate(int $length): string` + `validate(string $otp): bool`. Two implementations: `NumericGenerator`, `AlphanumericGenerator`.
- `OtpServiceProvider` — binds the `otp` singleton (registers all channels here), merges `config/otp.php`, loads migrations/views, registers the `otp:install` Artisan command.
- `Facades/Otp` — thin facade over the `otp` singleton. The `otp()` helper in `Support/helpers.php` is the same thing (`app('otp')`).

## Adding a new channel

1. Implement `ChannelInterface` in `src/Channels/`.
2. Register it in `OtpServiceProvider::register()`.
3. Add its config block under `channels` in `config/otp.php`, plus the matching env var(s) in `.env.example`.
4. Add a Pest test in `tests/Channels/` — fake the outbound call (`Http::fake()` for HTTP-based channels, `Mail::fake()` for email) and assert on the actual outgoing request/mailable.
5. Add a short usage example to `README.md`.

Do these in that order — a channel without config/tests/docs is exactly the kind of gap that existed before (`TelegramChannel` was documented and tested for a long time before it was actually implemented).

## Testing

- Pest only, backed by Orchestra Testbench (`tests/TestCase.php` boots a real Laravel app with `OtpServiceProvider` registered and an in-memory SQLite DB). Every test file is bound to this `TestCase` via `tests/Pest.php` — don't write raw PHPUnit test classes.
- Run: `composer test`. With coverage (needs Xdebug or PCOV): `composer test:coverage`.
- Any test touching a facade (`Http`, `Mail`, `Config`, `Log`) needs the Testbench app booted — that's automatic here, but if a test throws `RuntimeException: A facade root has not been set.`, it means it isn't running through `tests/TestCase.php`.
- `RefreshDatabase` runs the package's own migrations (`src/database/migrations/`) for every test — don't hand-roll schema setup in individual tests.
- `composer.json` pins one `orchestra/testbench` version for local dev (currently matched to the newest supported Laravel). CI (`.github/workflows/tests.yml`) matrixes actual Laravel versions (12 and 13, across their compatible PHP/testbench pairs) by swapping `laravel/framework`/`orchestra/testbench` per job — local `composer test` alone doesn't prove compatibility with the older Laravel versions still declared in `composer.json`'s `require`.

## Known footguns (fixed once, don't reintroduce)

- **README vs. real API**: keep usage examples in the README compiling against the actual `OtpService`/`ChannelInterface` signatures. This package previously had README examples for methods (`via()`, an array-returning `generateAndSend()`, a 3-arg `validate()`) that never existed in the code — write the example, then check it against the source before committing.
- **Path resolution in `OtpServiceProvider`**: `__DIR__` inside `src/OtpServiceProvider.php` is `.../src`. Migrations live at `src/database/migrations`, not `<package-root>/database/migrations`. Config lives at `<package-root>/config/otp.php`, not `resources/config/otp.php`. Double-check publish/merge paths after moving any package file.
- **Mail testing**: `Mail::fake()` + `Mail::assertSent()` only records real `Mailable` instances (see `src/Mail/OtpMail.php`) — the legacy `Mail::send($view, $data, $closure)` style is invisible to `MailFake` in modern Laravel and will silently no-op in tests.
- **`otp` binding name**: the singleton is registered as `'otp'` (`app('otp')`), not `'laravel-otp'` or the FQCN — `app(OtpService::class)` will construct a fresh instance with no channels registered instead of resolving the configured singleton.

## Conventions

- Follow `Contribution.md` for PR process (one feature per branch/PR, tests required, README updated).
- No comments explaining *what* code does — only non-obvious *why* (a workaround, a subtle invariant).
- Don't add speculative abstractions (interfaces with one implementation, config for values that never change) — this package intentionally stays small.
