<?php

declare(strict_types=1);

use l3aro\FilamentTurnstile\Facades\FilamentTurnstileFacade;

it('can verify cloudflare turnstile', function () {
    config()->set('filament-turnstile.key', '1x00000000000000000000AA');
    config()->set('filament-turnstile.secret', '1x0000000000000000000000000000000AA');

    $response = FilamentTurnstileFacade::verify('XXXX.DUMMY.TOKEN.XXXX');

    expect($response->success)->toBeTrue()
        ->and($response->isSuccess())->toBeTrue();
});

it('can verify cloudflare turnstile with fails response', function () {
    config()->set('filament-turnstile.key', '2x00000000000000000000AB');
    config()->set('filament-turnstile.secret', '2x0000000000000000000000000000000AA');

    $response = FilamentTurnstileFacade::verify('XXXX.DUMMY.TOKEN.XXXX');

    expect($response->success)->toBeFalse()
        ->and($response->isSuccess())->toBeFalse();
});

it('returns response metadata on successful verification', function () {
    config()->set('filament-turnstile.key', '1x00000000000000000000AA');
    config()->set('filament-turnstile.secret', '1x0000000000000000000000000000000AA');

    $response = FilamentTurnstileFacade::verify('XXXX.DUMMY.TOKEN.XXXX');

    expect($response->toArray())->toHaveKey('success', true);
});
