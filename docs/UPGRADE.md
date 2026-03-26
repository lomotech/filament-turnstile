# Upgrade Guide: v1.x to v2.0

This guide covers all breaking changes, new features, and migration steps when upgrading from filament-turnstile v1.x to v2.0.

## Table of Contents

- [Requirements Changes](#requirements-changes)
- [Breaking Changes](#breaking-changes)
- [New Configuration Options](#new-configuration-options)
- [New Enums](#new-enums)
- [New Turnstile Form Component Methods](#new-turnstile-form-component-methods)
- [FilamentTurnstileResponse Changes](#filamentturnstileresponse-changes)
- [TurnstileRule Changes](#turnstilerule-changes)
- [Migration Checklist](#migration-checklist)

## Requirements Changes

| Requirement | v1.x | v2.0 |
|---|---|---|
| PHP | ^8.2 | **^8.3** |
| Laravel | ^10.0 \| ^11.0 | **^11.0 \| ^12.0 \| ^13.0** |
| Filament | ^3.0 | **^4.0 \| ^5.0** |

**Action required:** Ensure your environment runs PHP 8.3 or later. If you are on Laravel 10, upgrade to Laravel 11+ before upgrading this package.

## Breaking Changes

### Readonly Properties on `FilamentTurnstileResponse`

All properties on `FilamentTurnstileResponse` are now declared `readonly`. If your code modifies response properties after construction, you must refactor to create a new instance instead.

```php
// v1.x -- mutable properties
$response = FilamentTurnstile::verify($token);
$response->success = false; // This worked in v1.x

// v2.0 -- readonly properties, will throw an Error
$response = FilamentTurnstile::verify($token);
$response->success = false; // Fatal error: Cannot modify readonly property
```

### New Response Properties

`FilamentTurnstileResponse` now includes additional fields from the Cloudflare API response:

- `challengeTs` (`?string`) -- ISO timestamp of when the challenge was solved.
- `hostname` (`?string`) -- The hostname for which the challenge was served.
- `action` (`?string`) -- The action name associated with the token, if configured.
- `cData` (`?string`) -- Custom data passed through the widget, if configured.

If you extend or mock `FilamentTurnstileResponse`, update your code to account for these new constructor parameters.

### Typed Class Constant

`FilamentTurnstile::SITEVERIFY_URL` is now a typed constant (`protected const string`). This is a PHP 8.3 feature and has no impact unless you were accessing it via reflection or extending the class and redeclaring the constant without a type.

### New Factory Method

`FilamentTurnstileResponse::fromResponse()` is the new named constructor used internally. The existing `make()` method is still available but only sets `success` and `errorCodes`. Use `fromResponse()` when you need the full response data.

### Filament v4 Dependency

The Blade view now uses Filament v4 features including `x-load-js` for asset loading and `ArgumentValue::Default` for nullable property defaults. The `HasAlignment` trait is imported from `Filament\Support\Concerns`.

## New Configuration Options

Three new configuration keys have been added to `config/filament-turnstile.php`:

```php
// config/filament-turnstile.php

// Number of retry attempts for the Cloudflare API call (default: 3)
'retry_times' => env('TURNSTILE_RETRY_TIMES', 3),

// Delay in milliseconds between retries (default: 100)
'retry_delay' => env('TURNSTILE_RETRY_DELAY', 100),

// HTTP timeout in seconds for the Cloudflare API call (default: 10)
'timeout' => env('TURNSTILE_TIMEOUT', 10),
```

**Action required:** If you have published the config file, add these three keys. Alternatively, re-publish the config:

```bash
php artisan vendor:publish --tag=filament-turnstile-config --force
```

## New Enums

v2.0 adds four new string-backed enums for type-safe widget configuration:

### `TurnstileAppearance`

Controls when the widget is visible.

| Case | Value | Description |
|---|---|---|
| `Always` | `"always"` | Widget is always visible |
| `Execute` | `"execute"` | Widget appears only during execution |
| `InteractionOnly` | `"interaction-only"` | Widget appears only when user interaction is needed |

### `TurnstileExecution`

Controls when the challenge begins.

| Case | Value | Description |
|---|---|---|
| `Render` | `"render"` | Challenge starts when widget renders |
| `Execute` | `"execute"` | Challenge starts only when explicitly triggered |

### `TurnstileRetry`

Controls automatic retry behavior on failure.

| Case | Value | Description |
|---|---|---|
| `Auto` | `"auto"` | Automatically retry on failure |
| `Never` | `"never"` | Do not retry on failure |

### `TurnstileRefreshStrategy`

Controls behavior when a token expires or times out. Used by both `refreshExpired` and `refreshTimeout`.

| Case | Value | Description |
|---|---|---|
| `Auto` | `"auto"` | Automatically refresh |
| `Manual` | `"manual"` | Require manual interaction to refresh |
| `Never` | `"never"` | Do not refresh |

### `TurnstileSize` -- Updated

A new case has been added:

| Case | Value | New in v2.0? |
|---|---|---|
| `Normal` | `"normal"` | No |
| `Compact` | `"compact"` | No |
| `Flexible` | `"flexible"` | **Yes** |

## New Turnstile Form Component Methods

The `Turnstile` form component now supports all Cloudflare Turnstile widget options:

```php
use l3aro\FilamentTurnstile\Forms\Turnstile;
use l3aro\FilamentTurnstile\Enums\TurnstileAppearance;
use l3aro\FilamentTurnstile\Enums\TurnstileExecution;
use l3aro\FilamentTurnstile\Enums\TurnstileRetry;
use l3aro\FilamentTurnstile\Enums\TurnstileRefreshStrategy;

Turnstile::make('captcha')
    // Existing methods
    ->theme(TurnstileTheme::Dark)
    ->size(TurnstileSize::Flexible)       // New Flexible option
    ->language('en')

    // New in v2.0
    ->appearance(TurnstileAppearance::InteractionOnly)
    ->execution(TurnstileExecution::Render)
    ->retry(TurnstileRetry::Auto)
    ->retryInterval(5000)                  // milliseconds
    ->refreshExpired(TurnstileRefreshStrategy::Auto)
    ->refreshTimeout(TurnstileRefreshStrategy::Manual)
    ->action('login')                      // action tag for analytics
    ->cData('custom-data')                 // custom data passthrough
    ->feedbackEnabled(true)                // Cloudflare feedback UI
```

All new methods accept closures for dynamic values, consistent with Filament's design patterns.

### Event Callbacks

Three new callback methods for handling widget lifecycle events:

```php
Turnstile::make('captcha')
    ->onExpired(function () {
        // Handle token expiration
    })
    ->onTimeout(function () {
        // Handle challenge timeout
    })
    ->onError(function () {
        // Handle widget error
    })
```

## FilamentTurnstileResponse Changes

### Constructor Signature

```php
// v2.0
public function __construct(
    public readonly bool $success,
    public readonly ?array $errorCodes = null,
    public readonly ?string $challengeTs = null,  // New
    public readonly ?string $hostname = null,      // New
    public readonly ?string $action = null,        // New
    public readonly ?string $cData = null,         // New
)
```

### New `fromResponse()` Factory Method

```php
$response = FilamentTurnstileResponse::fromResponse(
    success: true,
    errorCodes: null,
    challengeTs: '2024-01-15T10:30:00Z',
    hostname: 'example.com',
    action: 'login',
    cData: 'user-session-123',
);
```

### New `isExpired()` Method

```php
if ($response->isExpired()) {
    // Token was already used or expired
    // Checks for 'timeout-or-duplicate' in error codes
}
```

## TurnstileRule Changes

The constructor now accepts optional parameters for enhanced verification:

```php
// v1.x
new TurnstileRule();

// v2.0
new TurnstileRule(
    remoteIp: request()->ip(),         // Optional: client IP for verification
    idempotencyKey: $uniqueKey,        // Optional: prevent replay attacks
);
```

These parameters are forwarded to the Cloudflare siteverify API. The `remoteIp` is sent as `remoteip` and the `idempotencyKey` as `idempotency_key` in the verification payload.

## Migration Checklist

- [ ] Upgrade PHP to 8.3+
- [ ] Upgrade Laravel to 11+ (if on Laravel 10)
- [ ] Upgrade Filament to v4+
- [ ] Update `composer.json` to require `"l3aro/filament-turnstile": "^2.0"`
- [ ] Run `composer update l3aro/filament-turnstile`
- [ ] Re-publish or update config file with `retry_times`, `retry_delay`, `timeout` keys
- [ ] Review any code that modifies `FilamentTurnstileResponse` properties (now readonly)
- [ ] Review any code that extends or mocks `FilamentTurnstileResponse` (new constructor params)
- [ ] Update any custom `TurnstileRule` instantiation to use named parameters if needed
- [ ] Run your test suite to verify compatibility
