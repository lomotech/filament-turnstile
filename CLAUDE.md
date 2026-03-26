# Filament Turnstile v2.0

Filament plugin to integrate Cloudflare Turnstile. Laravel package using Filament v4+, PHP 8.3+.

## Commands

- **Test all**: `composer test`
- **Test single**: `vendor/bin/pest --filter="test name"` or `vendor/bin/pest tests/FileTest.php`
- **Lint/Static analysis**: `composer analyse` (use `--memory-limit=512M` if needed)
- **Format code**: `composer format`

## Rules

- Do not use `Co-Authored-By:` in commit messages
- Use PHPStan/Larastan level 5
- Use PHP 8 attributes (not docblock annotations)
- Use `#[\Override]` on all overridden methods
- Use `declare(strict_types=1)` in all PHP files
- Use readonly properties where applicable
- Follow Laravel Pint with "per" preset
- PHP 8.3+ with strict typing
- PSR-4 autoloading
- PascalCase for classes, camelCase for methods/properties, SCREAMING_SNAKE_CASE for constants
- Use Pest for testing with descriptive test names
- Follow Laravel package conventions (facades, service providers, enums)

## Architecture

- `src/` — Package source code
- `src/Enums/` — TurnstileSize, TurnstileTheme, TurnstileAppearance, TurnstileExecution, TurnstileRefreshStrategy, TurnstileRetry
- `src/Forms/` — Turnstile form component
- `src/Rules/` — TurnstileRule validation (supports remoteIp, idempotencyKey)
- `config/` — Package config (key, secret, reset_event, retry_times, retry_delay, timeout)
- `resources/` — Views and translations
- `tests/` — Pest tests
- `docs/` — Technical documentation (ARCHITECTURE.md, API.md, UPGRADE.md)
