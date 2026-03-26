<?php

declare(strict_types=1);

use l3aro\FilamentTurnstile\Enums\TurnstileAppearance;
use l3aro\FilamentTurnstile\Enums\TurnstileExecution;
use l3aro\FilamentTurnstile\Enums\TurnstileRefreshStrategy;
use l3aro\FilamentTurnstile\Enums\TurnstileRetry;
use l3aro\FilamentTurnstile\Enums\TurnstileSize;
use l3aro\FilamentTurnstile\Enums\TurnstileTheme;
use l3aro\FilamentTurnstile\Forms\Turnstile;

// Theme tests
it('can convert TurnstileTheme enum to string', function () {
    $component = Turnstile::make('captcha')
        ->theme(TurnstileTheme::Dark);

    expect($component->getTheme())->toBe('dark');
});

it('returns default theme when not set', function () {
    $component = Turnstile::make('captcha');

    expect($component->getTheme())->toBe('auto');
});

it('can accept string theme value', function () {
    $component = Turnstile::make('captcha')
        ->theme('light');

    expect($component->getTheme())->toBe('light');
});

// Size tests
it('can convert TurnstileSize enum to string', function () {
    $component = Turnstile::make('captcha')
        ->size(TurnstileSize::Compact);

    expect($component->getSize())->toBe('compact');
});

it('returns default size when not set', function () {
    $component = Turnstile::make('captcha');

    expect($component->getSize())->toBe('normal');
});

it('can accept string size value', function () {
    $component = Turnstile::make('captcha')
        ->size('normal');

    expect($component->getSize())->toBe('normal');
});

it('supports flexible size', function () {
    $component = Turnstile::make('captcha')
        ->size(TurnstileSize::Flexible);

    expect($component->getSize())->toBe('flexible');
});

// Appearance tests
it('can set appearance with enum', function () {
    $component = Turnstile::make('captcha')
        ->appearance(TurnstileAppearance::InteractionOnly);

    expect($component->getAppearance())->toBe('interaction-only');
});

it('can set appearance with string', function () {
    $component = Turnstile::make('captcha')
        ->appearance('execute');

    expect($component->getAppearance())->toBe('execute');
});

it('returns null appearance when not set', function () {
    $component = Turnstile::make('captcha');

    expect($component->getAppearance())->toBeNull();
});

// Execution tests
it('can set execution mode with enum', function () {
    $component = Turnstile::make('captcha')
        ->execution(TurnstileExecution::Execute);

    expect($component->getExecution())->toBe('execute');
});

it('returns null execution when not set', function () {
    $component = Turnstile::make('captcha');

    expect($component->getExecution())->toBeNull();
});

// Retry tests
it('can set retry with enum', function () {
    $component = Turnstile::make('captcha')
        ->retry(TurnstileRetry::Never);

    expect($component->getRetry())->toBe('never');
});

it('can set retry interval', function () {
    $component = Turnstile::make('captcha')
        ->retryInterval(5000);

    expect($component->getRetryInterval())->toBe(5000);
});

it('returns null retry interval when not set', function () {
    $component = Turnstile::make('captcha');

    expect($component->getRetryInterval())->toBeNull();
});

// Refresh strategy tests
it('can set refresh expired strategy', function () {
    $component = Turnstile::make('captcha')
        ->refreshExpired(TurnstileRefreshStrategy::Manual);

    expect($component->getRefreshExpired())->toBe('manual');
});

it('can set refresh timeout strategy', function () {
    $component = Turnstile::make('captcha')
        ->refreshTimeout(TurnstileRefreshStrategy::Never);

    expect($component->getRefreshTimeout())->toBe('never');
});

// Action and cData tests
it('can set action', function () {
    $component = Turnstile::make('captcha')
        ->turnstileAction('login');

    expect($component->getTurnstileAction())->toBe('login');
});

it('can set cData', function () {
    $component = Turnstile::make('captcha')
        ->cData('session-id-123');

    expect($component->getCData())->toBe('session-id-123');
});

it('returns null action when not set', function () {
    $component = Turnstile::make('captcha');

    expect($component->getTurnstileAction())->toBeNull();
});

it('returns null cData when not set', function () {
    $component = Turnstile::make('captcha');

    expect($component->getCData())->toBeNull();
});

// Feedback enabled tests
it('can set feedback enabled', function () {
    $component = Turnstile::make('captcha')
        ->feedbackEnabled(false);

    expect($component->getFeedbackEnabled())->toBeFalse();
});

it('returns null feedback enabled when not set', function () {
    $component = Turnstile::make('captcha');

    expect($component->getFeedbackEnabled())->toBeNull();
});

// Language tests
it('returns app locale as default language', function () {
    app()->setLocale('fr');

    $component = Turnstile::make('captcha');

    expect($component->getLanguage())->toBe('fr');
});

it('can set custom language', function () {
    $component = Turnstile::make('captcha')
        ->language('de');

    expect($component->getLanguage())->toBe('de');
});

// Reset event tests
it('returns config reset event as default', function () {
    config()->set('filament-turnstile.reset_event', 'my-reset');

    $component = Turnstile::make('captcha');

    expect($component->getResetEvent())->toBe('my-reset');
});

it('can set custom reset event', function () {
    $component = Turnstile::make('captcha')
        ->resetEvent('custom-reset');

    expect($component->getResetEvent())->toBe('custom-reset');
});
