# API Reference

Complete reference for all public classes, methods, and enums in filament-turnstile v2.0.

## Table of Contents

- [FilamentTurnstile](#filamentturnstile)
- [FilamentTurnstileResponse](#filamentturnstileresponse)
- [Turnstile Form Component](#turnstile-form-component)
- [TurnstileRule](#turnstilerule)
- [Enums](#enums)
- [Facade](#facade)
- [Configuration Reference](#configuration-reference)

---

## FilamentTurnstile

**Namespace:** `l3aro\FilamentTurnstile`

The core service class responsible for communicating with the Cloudflare Turnstile siteverify API.

### `verify()`

Verifies a Turnstile response token with the Cloudflare API.

```php
public function verify(
    string $responseCode,
    ?string $remoteIp = null,
    ?string $idempotencyKey = null,
): FilamentTurnstileResponse
```

**Parameters:**

| Parameter | Type | Required | Description |
|---|---|---|---|
| `$responseCode` | `string` | Yes | The Turnstile token from the client-side widget |
| `$remoteIp` | `?string` | No | The client's IP address for additional verification |
| `$idempotencyKey` | `?string` | No | A unique key to prevent duplicate verifications |

**Returns:** `FilamentTurnstileResponse`

**Behavior:**
- Sends a POST request to `https://challenges.cloudflare.com/turnstile/v0/siteverify`
- Payload includes `secret` (from config), `response` (the token), and optionally `remoteip` and `idempotency_key`
- Uses retry logic configured via `retry_times` and `retry_delay`
- HTTP timeout configured via `timeout`

**Example:**

```php
use l3aro\FilamentTurnstile\Facades\FilamentTurnstileFacade;

$response = FilamentTurnstileFacade::verify(
    responseCode: $request->input('cf-turnstile-response'),
    remoteIp: $request->ip(),
);

if ($response->isSuccess()) {
    // Token is valid
}
```

### `getResetEventName()`

Returns the Livewire event name used to reset the Turnstile widget.

```php
public function getResetEventName(): string
```

**Returns:** `string` -- The event name from `config('filament-turnstile.reset_event')`.

---

## FilamentTurnstileResponse

**Namespace:** `l3aro\FilamentTurnstile`

Immutable value object representing the Cloudflare siteverify API response. Implements `Illuminate\Contracts\Support\Arrayable`.

### Constructor

```php
public function __construct(
    public readonly bool $success,
    public readonly ?array $errorCodes = null,
    public readonly ?string $challengeTs = null,
    public readonly ?string $hostname = null,
    public readonly ?string $action = null,
    public readonly ?string $cData = null,
)
```

### Properties

| Property | Type | Description |
|---|---|---|
| `$success` | `bool` | Whether the token verification succeeded |
| `$errorCodes` | `?array` | Array of Cloudflare error code strings, or null |
| `$challengeTs` | `?string` | ISO 8601 timestamp of when the challenge was solved |
| `$hostname` | `?string` | The hostname for which the challenge was served |
| `$action` | `?string` | The action name, if one was configured on the widget |
| `$cData` | `?string` | Custom data, if passed through the widget |

All properties are `readonly`.

### `make()`

Static factory that creates a response with only success and error codes.

```php
public static function make(bool $success, ?array $errorCodes): self
```

### `fromResponse()`

Static factory that creates a full response from all Cloudflare API fields.

```php
public static function fromResponse(
    bool $success,
    ?array $errorCodes = null,
    ?string $challengeTs = null,
    ?string $hostname = null,
    ?string $action = null,
    ?string $cData = null,
): self
```

### `isSuccess()`

```php
public function isSuccess(): bool
```

Returns `true` if the token was verified successfully.

### `isExpired()`

```php
public function isExpired(): bool
```

Returns `true` if `errorCodes` contains `'timeout-or-duplicate'`, indicating the token was already used or has expired.

### `toArray()`

```php
public function toArray(): array
```

Returns an associative array of all non-null properties. Keys: `success`, `errorCodes`, `challengeTs`, `hostname`, `action`, `cData`.

---

## Turnstile Form Component

**Namespace:** `l3aro\FilamentTurnstile\Forms`

**Extends:** `Filament\Forms\Components\Field`

**Uses:** `Filament\Support\Concerns\HasAlignment`

The primary class developers interact with. Added to Filament forms as a field component.

### Basic Usage

```php
use l3aro\FilamentTurnstile\Forms\Turnstile;

public function form(Form $form): Form
{
    return $form->schema([
        // ... other fields
        Turnstile::make('captcha'),
    ]);
}
```

### Default Behavior

When created via `make()`, the component automatically:

- Sets the field as `required`
- Hides the label (`hiddenLabel()`)
- Attaches `TurnstileRule` for server-side validation
- Sets `dehydrated(false)` so the token is not stored with form data

### Builder Methods -- Widget Appearance

#### `theme()`

```php
public function theme(
    string|Closure|ArgumentValue|TurnstileTheme|null $theme
): static
```

Sets the visual theme of the widget. Default: `'auto'`.

#### `size()`

```php
public function size(
    string|Closure|ArgumentValue|TurnstileSize|null $size
): static
```

Sets the widget size. Default: `'normal'`.

#### `language()`

```php
public function language(
    string|Closure|ArgumentValue|null $language
): static
```

Sets the widget language. Default: the application's current locale (`app()->getLocale()`).

#### `appearance()`

```php
public function appearance(
    string|Closure|ArgumentValue|TurnstileAppearance|null $appearance
): static
```

Controls when the widget is visible. Default: not set (Cloudflare default).

#### `feedbackEnabled()`

```php
public function feedbackEnabled(
    bool|Closure|ArgumentValue $feedbackEnabled = true
): static
```

Enables or disables the Cloudflare feedback UI. Default: not set (Cloudflare default).

### Builder Methods -- Widget Behavior

#### `execution()`

```php
public function execution(
    string|Closure|ArgumentValue|TurnstileExecution|null $execution
): static
```

Controls when the challenge begins. Default: not set (Cloudflare default, equivalent to `render`).

#### `retry()`

```php
public function retry(
    string|Closure|ArgumentValue|TurnstileRetry|null $retry
): static
```

Controls automatic retry on failure. Default: not set (Cloudflare default).

#### `retryInterval()`

```php
public function retryInterval(
    int|Closure|ArgumentValue|null $retryInterval
): static
```

Sets the retry interval in milliseconds. Default: not set (Cloudflare default, 8000ms).

#### `refreshExpired()`

```php
public function refreshExpired(
    string|Closure|ArgumentValue|TurnstileRefreshStrategy|null $refreshExpired
): static
```

Controls behavior when the token expires. Default: not set (Cloudflare default).

#### `refreshTimeout()`

```php
public function refreshTimeout(
    string|Closure|ArgumentValue|TurnstileRefreshStrategy|null $refreshTimeout
): static
```

Controls behavior when the challenge times out. Default: not set (Cloudflare default).

### Builder Methods -- Data and Tracking

#### `action()`

```php
public function action(
    string|Closure|null $action
): static
```

Sets an action tag for analytics in the Cloudflare dashboard. The value is included in the verification response. Default: `null`.

#### `cData()`

```php
public function cData(
    string|Closure|null $cData
): static
```

Passes custom data through the widget. The value is included in the verification response. Default: `null`.

### Builder Methods -- Events

#### `resetEvent()`

```php
public function resetEvent(
    string|Closure|ArgumentValue|null $resetEvent
): static
```

Overrides the Livewire event name used to reset the widget. Default: value from `config('filament-turnstile.reset_event')`.

#### `onExpired()`

```php
public function onExpired(?Closure $callback): static
```

Sets a callback for when the token expires.

#### `onTimeout()`

```php
public function onTimeout(?Closure $callback): static
```

Sets a callback for when the challenge times out.

#### `onError()`

```php
public function onError(?Closure $callback): static
```

Sets a callback for when an error occurs.

### Builder Methods -- Layout

#### Alignment

Inherited from `HasAlignment`. Controls horizontal alignment of the widget.

```php
Turnstile::make('captcha')
    ->alignment(Alignment::Center)
```

The `getAlignmentClasses()` method returns Tailwind CSS justify classes:

| Alignment | CSS Class |
|---|---|
| `Center` | `justify-center` |
| `Left` / `Start` | `justify-start` |
| `Right` / `End` | `justify-end` |
| `Between` | `justify-between` |

### Full Example

```php
use l3aro\FilamentTurnstile\Forms\Turnstile;
use l3aro\FilamentTurnstile\Enums\TurnstileTheme;
use l3aro\FilamentTurnstile\Enums\TurnstileSize;
use l3aro\FilamentTurnstile\Enums\TurnstileAppearance;
use l3aro\FilamentTurnstile\Enums\TurnstileExecution;
use l3aro\FilamentTurnstile\Enums\TurnstileRetry;
use l3aro\FilamentTurnstile\Enums\TurnstileRefreshStrategy;
use Filament\Support\Enums\Alignment;

Turnstile::make('captcha')
    ->theme(TurnstileTheme::Dark)
    ->size(TurnstileSize::Flexible)
    ->language('en')
    ->appearance(TurnstileAppearance::Always)
    ->execution(TurnstileExecution::Render)
    ->retry(TurnstileRetry::Auto)
    ->retryInterval(5000)
    ->refreshExpired(TurnstileRefreshStrategy::Auto)
    ->refreshTimeout(TurnstileRefreshStrategy::Auto)
    ->action('registration')
    ->cData('form-signup')
    ->feedbackEnabled(true)
    ->alignment(Alignment::Center)
```

---

## TurnstileRule

**Namespace:** `l3aro\FilamentTurnstile\Rules`

**Implements:** `Illuminate\Contracts\Validation\ValidationRule`

### Constructor

```php
public function __construct(
    protected ?string $remoteIp = null,
    protected ?string $idempotencyKey = null,
)
```

| Parameter | Type | Required | Description |
|---|---|---|---|
| `$remoteIp` | `?string` | No | Client IP address for enhanced verification |
| `$idempotencyKey` | `?string` | No | Unique key to prevent duplicate verifications |

### `validate()`

```php
public function validate(
    string $attribute,
    mixed $value,
    Closure $fail,
): void
```

Called by Laravel's validation system. The method:

1. Rejects empty or non-string values with the `missing-input-response` error.
2. Calls `FilamentTurnstile::verify()` with the token and optional parameters.
3. On success, validation passes silently.
4. On failure with no error codes, returns the `unexpected` error.
5. On failure with error codes, returns a translated error for each code.

### Standalone Usage

Outside of Filament forms, the rule can be used directly in Laravel validation:

```php
use l3aro\FilamentTurnstile\Rules\TurnstileRule;

$request->validate([
    'cf-turnstile-response' => [
        'required',
        new TurnstileRule(
            remoteIp: $request->ip(),
            idempotencyKey: $request->session()->getId(),
        ),
    ],
]);
```

---

## Enums

All enums are string-backed and located in the `l3aro\FilamentTurnstile\Enums` namespace.

### TurnstileTheme

Controls the visual theme of the widget.

```php
enum TurnstileTheme: string
{
    case Auto = 'auto';
    case Light = 'light';
    case Dark = 'dark';
}
```

### TurnstileSize

Controls the size of the widget.

```php
enum TurnstileSize: string
{
    case Normal = 'normal';
    case Flexible = 'flexible';
    case Compact = 'compact';
}
```

### TurnstileAppearance

Controls when the widget is visible to the user.

```php
enum TurnstileAppearance: string
{
    case Always = 'always';
    case Execute = 'execute';
    case InteractionOnly = 'interaction-only';
}
```

### TurnstileExecution

Controls when the challenge execution begins.

```php
enum TurnstileExecution: string
{
    case Render = 'render';
    case Execute = 'execute';
}
```

### TurnstileRetry

Controls automatic retry behavior.

```php
enum TurnstileRetry: string
{
    case Auto = 'auto';
    case Never = 'never';
}
```

### TurnstileRefreshStrategy

Controls refresh behavior for expired tokens and timeouts.

```php
enum TurnstileRefreshStrategy: string
{
    case Auto = 'auto';
    case Manual = 'manual';
    case Never = 'never';
}
```

---

## Facade

**Class:** `l3aro\FilamentTurnstile\Facades\FilamentTurnstileFacade`

**Alias:** `FilamentTurnstile` (registered in `composer.json` Laravel extra)

### Available Methods

```php
FilamentTurnstileFacade::verify(
    string $response,
    ?string $remoteIp = null,
    ?string $idempotencyKey = null,
): FilamentTurnstileResponse

FilamentTurnstileFacade::getResetEventName(): string
```

---

## Configuration Reference

**File:** `config/filament-turnstile.php`

Publish with:

```bash
php artisan vendor:publish --tag=filament-turnstile-config
```

| Key | Env Variable | Default | Type | Description |
|---|---|---|---|---|
| `key` | `TURNSTILE_SITE_KEY` | `null` | `?string` | Cloudflare Turnstile site key from the dashboard |
| `secret` | `TURNSTILE_SECRET_KEY` | `null` | `?string` | Cloudflare Turnstile secret key for server-side verification |
| `reset_event` | `TURNSTILE_RESET_EVENT` | `'reset-captcha'` | `string` | Livewire event name to reset the widget |
| `retry_times` | `TURNSTILE_RETRY_TIMES` | `3` | `int` | Number of HTTP retry attempts for the siteverify API |
| `retry_delay` | `TURNSTILE_RETRY_DELAY` | `100` | `int` | Delay between retries in milliseconds |
| `timeout` | `TURNSTILE_TIMEOUT` | `10` | `int` | HTTP timeout in seconds for the siteverify API call |

### Environment Variables

Add these to your `.env` file:

```dotenv
TURNSTILE_SITE_KEY=your-site-key
TURNSTILE_SECRET_KEY=your-secret-key

# Optional
TURNSTILE_RESET_EVENT=reset-captcha
TURNSTILE_RETRY_TIMES=3
TURNSTILE_RETRY_DELAY=100
TURNSTILE_TIMEOUT=10
```

### Cloudflare Test Keys

For local development and testing, Cloudflare provides test keys:

| Type | Site Key | Secret Key |
|---|---|---|
| Always passes | `1x00000000000000000000AA` | `1x0000000000000000000000000000000AA` |
| Always fails | `2x00000000000000000000AB` | `2x0000000000000000000000000000000AB` |
| Forces interactive | `3x00000000000000000000FF` | `3x0000000000000000000000000000000FF` |
