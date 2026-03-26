<?php

declare(strict_types=1);

use l3aro\FilamentTurnstile\FilamentTurnstileResponse;

it('can create response with make', function () {
    $response = FilamentTurnstileResponse::make(success: true, errorCodes: null);

    expect($response->success)->toBeTrue()
        ->and($response->errorCodes)->toBeNull();
});

it('can create response with fromResponse', function () {
    $response = FilamentTurnstileResponse::fromResponse(
        success: true,
        errorCodes: null,
        challengeTs: '2026-03-26T00:00:00.000Z',
        hostname: 'example.com',
        action: 'login',
        cData: 'session-123',
    );

    expect($response->success)->toBeTrue()
        ->and($response->challengeTs)->toBe('2026-03-26T00:00:00.000Z')
        ->and($response->hostname)->toBe('example.com')
        ->and($response->action)->toBe('login')
        ->and($response->cData)->toBe('session-123');
});

it('detects expired token', function () {
    $response = FilamentTurnstileResponse::make(
        success: false,
        errorCodes: ['timeout-or-duplicate'],
    );

    expect($response->isExpired())->toBeTrue();
});

it('reports not expired for valid response', function () {
    $response = FilamentTurnstileResponse::make(
        success: true,
        errorCodes: null,
    );

    expect($response->isExpired())->toBeFalse();
});

it('converts to array excluding null values', function () {
    $response = FilamentTurnstileResponse::make(success: true, errorCodes: null);

    $array = $response->toArray();

    expect($array)->toHaveKey('success', true)
        ->and($array)->not->toHaveKey('errorCodes')
        ->and($array)->not->toHaveKey('challengeTs');
});

it('converts to array including all set values', function () {
    $response = FilamentTurnstileResponse::fromResponse(
        success: false,
        errorCodes: ['invalid-input-response'],
        challengeTs: '2026-03-26T00:00:00.000Z',
        hostname: 'example.com',
    );

    $array = $response->toArray();

    expect($array)->toHaveKeys(['success', 'errorCodes', 'challengeTs', 'hostname'])
        ->and($array['success'])->toBeFalse()
        ->and($array['errorCodes'])->toBe(['invalid-input-response']);
});

it('has readonly properties', function () {
    $response = FilamentTurnstileResponse::make(success: true, errorCodes: null);

    expect($response)->toHaveProperties(['success', 'errorCodes', 'challengeTs', 'hostname', 'action', 'cData']);
});
