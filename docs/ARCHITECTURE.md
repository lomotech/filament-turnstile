# Architecture Overview

Filament Turnstile is a Laravel package that integrates Cloudflare Turnstile CAPTCHA into Filament v4+ forms. It provides a drop-in form component, server-side token verification, and type-safe configuration through PHP enums.

## Table of Contents

- [System Overview](#system-overview)
- [Component Diagram](#component-diagram)
- [Data Flow](#data-flow)
- [Key Classes](#key-classes)
- [Blade View and Alpine.js](#blade-view-and-alpinejs)
- [Enum Configuration Types](#enum-configuration-types)
- [Service Provider and Registration](#service-provider-and-registration)
- [Error Handling](#error-handling)

## System Overview

The package bridges three systems:

1. **Cloudflare Turnstile** -- the external CAPTCHA service that issues and verifies challenge tokens.
2. **Filament Forms** -- the UI framework where the Turnstile widget renders as a form field.
3. **Laravel HTTP + Validation** -- the server-side layer that verifies tokens against the Cloudflare API before accepting form submissions.

The widget runs entirely client-side using Cloudflare's JavaScript SDK and Alpine.js. When the user completes the challenge, a token is sent to the server via Livewire. On form submission, the `TurnstileRule` validation rule calls the Cloudflare siteverify API to confirm the token is valid.

## Component Diagram

```mermaid
graph TB
    subgraph Browser["Browser (Client)"]
        BV["Blade View<br/>turnstile.blade.php"]
        AJ["Alpine.js Component"]
        CF_JS["Cloudflare Turnstile JS SDK"]
        BV --> AJ
        AJ --> CF_JS
    end

    subgraph Server["Laravel Application (Server)"]
        LW["Livewire"]
        TC["Turnstile<br/>Form Component"]
        TR["TurnstileRule<br/>Validation Rule"]
        FT["FilamentTurnstile<br/>HTTP Client"]
        FTR["FilamentTurnstileResponse<br/>Value Object"]
    end

    subgraph External["External Service"]
        CF_API["Cloudflare Siteverify API<br/>challenges.cloudflare.com/turnstile/v0/siteverify"]
    end

    CF_JS -- "challenge token" --> AJ
    AJ -- "$wire.set(token)" --> LW
    LW -- "state binding" --> TC
    TC -- "validation" --> TR
    TR -- "verify(token)" --> FT
    FT -- "HTTP POST" --> CF_API
    CF_API -- "JSON response" --> FT
    FT -- "parse" --> FTR
    FTR -- "success/failure" --> TR
```

## Data Flow

The following sequence describes the full lifecycle of a Turnstile verification during form submission.

```mermaid
sequenceDiagram
    participant User
    participant Browser as Alpine.js + Blade View
    participant CF_JS as Cloudflare JS SDK
    participant LW as Livewire
    participant Rule as TurnstileRule
    participant Client as FilamentTurnstile
    participant CF_API as Cloudflare API

    User->>Browser: Loads page with Turnstile form field
    Browser->>CF_JS: turnstile.render(element, options)
    CF_JS->>User: Displays challenge widget
    User->>CF_JS: Completes challenge (or auto-pass)
    CF_JS->>Browser: Callback with token string
    Browser->>LW: $wire.set(statePath, token)

    User->>LW: Submits form
    LW->>Rule: validate(attribute, token, fail)
    Rule->>Client: verify(token, remoteIp?, idempotencyKey?)
    Client->>CF_API: POST {secret, response, remoteip?, idempotency_key?}
    CF_API-->>Client: {success, error-codes, challenge_ts, hostname, action, cdata}
    Client-->>Rule: FilamentTurnstileResponse

    alt Token valid
        Rule-->>LW: Validation passes
        LW-->>User: Form submission succeeds
    else Token invalid or expired
        Rule-->>LW: Validation fails with translated error
        LW-->>User: Error message displayed
    end
```

## Key Classes

### `FilamentTurnstile` -- HTTP Client

**Namespace:** `l3aro\FilamentTurnstile`

The core service class. Sends token verification requests to the Cloudflare siteverify endpoint. Registered as a singleton in the container and accessible via the `FilamentTurnstileFacade` facade.

**Responsibilities:**
- Builds the verification payload (secret, response token, optional remoteip and idempotency_key).
- Sends an HTTP POST with configurable retry logic (`retry_times`, `retry_delay`) and timeout.
- Parses the Cloudflare JSON response into a `FilamentTurnstileResponse` value object.
- Exposes the configured reset event name.

### `FilamentTurnstileResponse` -- Value Object

**Namespace:** `l3aro\FilamentTurnstile`

An immutable value object (all properties are `readonly`) that represents the Cloudflare API response. Implements `Arrayable` for easy serialization.

**Properties:** `success`, `errorCodes`, `challengeTs`, `hostname`, `action`, `cData`.

**Key methods:** `isSuccess()`, `isExpired()`, `toArray()`.

### `Turnstile` -- Filament Form Component

**Namespace:** `l3aro\FilamentTurnstile\Forms`

Extends `Filament\Forms\Components\Field`. This is the main integration point for developers. Added to Filament forms like any other field component.

**Responsibilities:**
- Configures the Turnstile widget options (theme, size, appearance, execution mode, retry behavior, refresh strategies, action, cData, feedback).
- Automatically applies the `TurnstileRule` validation rule.
- Renders via the `filament-turnstile::forms.turnstile` Blade view.
- Supports Filament alignment via the `HasAlignment` trait.
- Marked as `required`, `hiddenLabel`, and `dehydrated(false)` by default (the token is transient and never stored).

### `TurnstileRule` -- Validation Rule

**Namespace:** `l3aro\FilamentTurnstile\Rules`

Implements Laravel's `ValidationRule` interface. Called during form validation to verify the Turnstile token server-side.

**Responsibilities:**
- Rejects empty or non-string values with a translated error.
- Calls `FilamentTurnstile::verify()` with optional `remoteIp` and `idempotencyKey`.
- Maps Cloudflare error codes to translated error messages via the `filament-turnstile::errors` language file.

### `FilamentTurnstileFacade` -- Facade

**Namespace:** `l3aro\FilamentTurnstile\Facades`

Standard Laravel facade proxying to the `FilamentTurnstile` service class. Provides static access to `verify()` and `getResetEventName()`.

### `FilamentTurnstileServiceProvider` -- Service Provider

**Namespace:** `l3aro\FilamentTurnstile`

Uses `spatie/laravel-package-tools` for package registration. Handles config, translations, views, and the install command. Mixes `TestsFilamentTurnstile` into Livewire's `Testable` class on boot.

## Blade View and Alpine.js

The Blade template (`resources/views/forms/turnstile.blade.php`) orchestrates the client-side widget:

1. **Script loading** -- Uses Filament's `x-load-js` to load the Cloudflare Turnstile SDK with `render=explicit` mode.
2. **Alpine.js component** -- Manages widget state (`widgetId`) and Livewire state binding via `$wire.entangle`.
3. **Widget rendering** -- Calls `turnstile.render()` with options derived from the PHP component's getter methods.
4. **Callbacks** -- On success, sets the Livewire state to the token. On error, expiry, or timeout, clears the state to `null`.
5. **Reset support** -- Listens for a configurable Livewire event to reset the widget.
6. **Cleanup** -- Removes the widget when the Alpine component is destroyed.

```mermaid
stateDiagram-v2
    [*] --> Loading: Page loads
    Loading --> Ready: Cloudflare SDK loaded
    Ready --> Rendering: turnstile.render() called
    Rendering --> Waiting: Widget displayed
    Waiting --> Solved: User completes challenge
    Solved --> TokenSent: $wire.set(token)
    TokenSent --> [*]: Form submitted

    Waiting --> Expired: Token expired
    Expired --> Rendering: refreshExpired=auto

    Waiting --> TimedOut: Challenge timed out
    TimedOut --> Rendering: refreshTimeout=auto

    Waiting --> Error: Error occurred
    Error --> Rendering: retry=auto

    TokenSent --> Rendering: reset event received
```

## Enum Configuration Types

All enums are string-backed and located in `l3aro\FilamentTurnstile\Enums`.

```mermaid
classDiagram
    class TurnstileTheme {
        <<enum>>
        Auto = "auto"
        Light = "light"
        Dark = "dark"
    }

    class TurnstileSize {
        <<enum>>
        Normal = "normal"
        Flexible = "flexible"
        Compact = "compact"
    }

    class TurnstileAppearance {
        <<enum>>
        Always = "always"
        Execute = "execute"
        InteractionOnly = "interaction-only"
    }

    class TurnstileExecution {
        <<enum>>
        Render = "render"
        Execute = "execute"
    }

    class TurnstileRetry {
        <<enum>>
        Auto = "auto"
        Never = "never"
    }

    class TurnstileRefreshStrategy {
        <<enum>>
        Auto = "auto"
        Manual = "manual"
        Never = "never"
    }

    class Turnstile {
        +theme(TurnstileTheme)
        +size(TurnstileSize)
        +appearance(TurnstileAppearance)
        +execution(TurnstileExecution)
        +retry(TurnstileRetry)
        +refreshExpired(TurnstileRefreshStrategy)
        +refreshTimeout(TurnstileRefreshStrategy)
    }

    Turnstile --> TurnstileTheme : uses
    Turnstile --> TurnstileSize : uses
    Turnstile --> TurnstileAppearance : uses
    Turnstile --> TurnstileExecution : uses
    Turnstile --> TurnstileRetry : uses
    Turnstile --> TurnstileRefreshStrategy : uses
```

## Error Handling

The package translates Cloudflare error codes into user-facing messages. The translation keys live in `resources/lang/en/errors.php`:

| Cloudflare Error Code | Translation Key | Default Message |
|---|---|---|
| `missing-input-secret` | `filament-turnstile::errors.missing-input-secret` | The secret key is missing. |
| `invalid-input-secret` | `filament-turnstile::errors.invalid-input-secret` | The secret key is invalid. |
| `missing-input-response` | `filament-turnstile::errors.missing-input-response` | The response is missing. |
| `invalid-input-response` | `filament-turnstile::errors.invalid-input-response` | The response is invalid or has expired. |
| `bad-request` | `filament-turnstile::errors.bad-request` | The request was rejected because it was malformed. |
| `timeout-or-duplicate` | `filament-turnstile::errors.timeout-or-duplicate` | The response has already been validated before. |
| `internal-error` | `filament-turnstile::errors.internal-error` | An internal error happened while validating the response. |
| (no error codes) | `filament-turnstile::errors.unexpected` | An unexpected error occurred. |

Error messages can be customized by publishing the language files or adding translations for additional locales.
