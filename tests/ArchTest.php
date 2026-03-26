<?php

declare(strict_types=1);

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

it('uses strict types in all source files')
    ->expect('l3aro\FilamentTurnstile')
    ->toUseStrictTypes();

it('enums are backed string enums')
    ->expect('l3aro\FilamentTurnstile\Enums')
    ->toBeStringBackedEnums();
