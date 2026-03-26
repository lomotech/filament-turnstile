<?php

declare(strict_types=1);

namespace l3aro\FilamentTurnstile;

use Illuminate\Support\Facades\Http;

class FilamentTurnstile
{
    protected const string SITEVERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function verify(string $responseCode, ?string $remoteIp = null, ?string $idempotencyKey = null): FilamentTurnstileResponse
    {
        $payload = [
            'secret' => config('filament-turnstile.secret'),
            'response' => $responseCode,
        ];

        if ($remoteIp !== null) {
            $payload['remoteip'] = $remoteIp;
        }

        if ($idempotencyKey !== null) {
            $payload['idempotency_key'] = $idempotencyKey;
        }

        $response = Http::retry(
            times: (int) config('filament-turnstile.retry_times', 3),
            sleepMilliseconds: (int) config('filament-turnstile.retry_delay', 100),
        )
            ->asJson()
            ->acceptJson()
            ->timeout((int) config('filament-turnstile.timeout', 10))
            ->post(self::SITEVERIFY_URL, $payload);

        return FilamentTurnstileResponse::fromResponse(
            success: $response->ok() && $response->json('success'),
            errorCodes: $response->json('error-codes'),
            challengeTs: $response->json('challenge_ts'),
            hostname: $response->json('hostname'),
            action: $response->json('action'),
            cData: $response->json('cdata'),
        );
    }

    public function getResetEventName(): string
    {
        return config('filament-turnstile.reset_event');
    }
}
