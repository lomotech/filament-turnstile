# Agent Instructions for l3aro/filament-turnstile v2.0

## Commands
- **Test all**: `composer test` or `vendor/bin/pest`
- **Test single**: `vendor/bin/pest --filter="test name"` or `vendor/bin/pest tests/FileTest.php`
- **Lint/Static analysis**: `composer analyse` or `vendor/bin/phpstan analyse --memory-limit=512M`
- **Format code**: `composer format` or `vendor/bin/pint`

## Code Style
- **PHP**: 8.3+ with strict typing (`declare(strict_types=1)`) and PSR-4 autoloading
- **Formatting**: Laravel Pint with "per" preset (4-space indentation, UTF-8, LF endings)
- **Static analysis**: PHPStan level 5
- **Testing**: Pest framework with descriptive test names
- **Naming**: PascalCase for classes, camelCase for methods/properties, SCREAMING_SNAKE_CASE for constants
- **Imports**: Group by type (classes, functions, constants) with blank lines between groups
- **PHP 8.3 features**: Use typed class constants, `#[\Override]` attribute, readonly properties
- **Architecture**: Follow Laravel package conventions (facades, service providers, enums for constants)
