<?php

declare(strict_types=1);

namespace l3aro\FilamentTurnstile\Facades;

use Illuminate\Support\Facades\Facade;
use l3aro\FilamentTurnstile\FilamentTurnstileResponse;

/**
 * @method static FilamentTurnstileResponse verify(string $response, ?string $remoteIp = null, ?string $idempotencyKey = null)
 * @method static string getResetEventName()
 *
 * @see \l3aro\FilamentTurnstile\FilamentTurnstile
 */
class FilamentTurnstileFacade extends Facade
{
    #[\Override]
    protected static function getFacadeAccessor(): string
    {
        return \l3aro\FilamentTurnstile\FilamentTurnstile::class;
    }
}
