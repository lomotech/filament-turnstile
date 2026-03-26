<?php

declare(strict_types=1);

namespace l3aro\FilamentTurnstile\Enums;

enum TurnstileAppearance: string
{
    case Always = 'always';
    case Execute = 'execute';
    case InteractionOnly = 'interaction-only';
}
