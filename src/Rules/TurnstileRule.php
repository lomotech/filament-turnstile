<?php

declare(strict_types=1);

namespace l3aro\FilamentTurnstile\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use l3aro\FilamentTurnstile\Facades\FilamentTurnstileFacade;

class TurnstileRule implements ValidationRule
{
    public function __construct(
        protected ?string $remoteIp = null,
        protected ?string $idempotencyKey = null,
    ) {}

    #[\Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || ! is_string($value)) {
            $fail('filament-turnstile::errors.missing-input-response')->translate();

            return;
        }

        $response = FilamentTurnstileFacade::verify($value, $this->remoteIp, $this->idempotencyKey);

        if ($response->success) {
            return;
        }

        if (empty($response->errorCodes)) {
            $fail('filament-turnstile::errors.unexpected')->translate();

            return;
        }

        foreach ($response->errorCodes as $errorCode) {
            $fail('filament-turnstile::errors.' . $errorCode)->translate();
        }
    }
}
