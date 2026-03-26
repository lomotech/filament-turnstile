# Filament Turnstile

Filament plugin to integrate Cloudflare Turnstile. Laravel package using Filament v4+, PHP 8.2+.

## Commands

- **Test all**: `composer test`
- **Test single**: `vendor/bin/pest --filter="test name"` or `vendor/bin/pest tests/FileTest.php`
- **Lint/Static analysis**: `composer analyse`
- **Format code**: `composer format`

## Rules

- Do not use `Co-Authored-By:` in commit messages
- Use PHPStan/Larastan level 5 (upgrade from current level 4)
- Use PHP 8 attributes (not docblock annotations)
- Follow Laravel Pint with "per" preset
- PHP 8.2+ with strict typing
- PSR-4 autoloading
- PascalCase for classes, camelCase for methods/properties, SCREAMING_SNAKE_CASE for constants
- Use Pest for testing with descriptive test names
- Follow Laravel package conventions (facades, service providers, enums)

## Architecture

- `src/` — Package source code
- `src/Enums/` — TurnstileSize, TurnstileTheme
- `src/Forms/` — Turnstile form component
- `src/Rules/` — TurnstileRule validation
- `config/` — Package config
- `resources/` — Views and assets
- `tests/` — Pest tests
