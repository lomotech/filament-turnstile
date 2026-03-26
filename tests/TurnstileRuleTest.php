<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Validator;
use l3aro\FilamentTurnstile\Rules\TurnstileRule;

it('can validate cloudflare turnstile', function () {
    config()->set('filament-turnstile.key', '1x00000000000000000000AA');
    config()->set('filament-turnstile.secret', '1x0000000000000000000000000000000AA');

    $validator = Validator::make([
        'turnstile' => 'XXXX.DUMMY.TOKEN.XXXX',
    ], [
        'turnstile' => new TurnstileRule(),
    ]);

    expect($validator->passes())->toBeTrue();
});

it('can block cloudflare turnstile with invalid token', function () {
    config()->set('filament-turnstile.key', '2x00000000000000000000AB');
    config()->set('filament-turnstile.secret', '2x0000000000000000000000000000000AA');

    $validator = Validator::make([
        'turnstile' => 'XXXX.DUMMY.TOKEN.XXXX',
    ], [
        'turnstile' => new TurnstileRule(),
    ]);

    expect($validator->fails())->toBeTrue();
});

it('fails validation with empty token', function () {
    config()->set('filament-turnstile.key', '1x00000000000000000000AA');
    config()->set('filament-turnstile.secret', '1x0000000000000000000000000000000AA');

    $validator = Validator::make([
        'turnstile' => '',
    ], [
        'turnstile' => ['required', new TurnstileRule()],
    ]);

    expect($validator->fails())->toBeTrue();
});

it('fails validation with null token', function () {
    config()->set('filament-turnstile.key', '1x00000000000000000000AA');
    config()->set('filament-turnstile.secret', '1x0000000000000000000000000000000AA');

    $validator = Validator::make([
        'turnstile' => null,
    ], [
        'turnstile' => new TurnstileRule(),
    ]);

    expect($validator->fails())->toBeTrue();
});

it('can accept idempotency key', function () {
    config()->set('filament-turnstile.key', '1x00000000000000000000AA');
    config()->set('filament-turnstile.secret', '1x0000000000000000000000000000000AA');

    $rule = new TurnstileRule(idempotencyKey: 'test-idempotency-key');

    $validator = Validator::make([
        'turnstile' => 'XXXX.DUMMY.TOKEN.XXXX',
    ], [
        'turnstile' => $rule,
    ]);

    expect($validator->passes())->toBeTrue();
});
