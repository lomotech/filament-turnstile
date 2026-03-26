<?php

declare(strict_types=1);

namespace l3aro\FilamentTurnstile\Enums;

enum TurnstileSize: string
{
    case Normal = 'normal';
    case Flexible = 'flexible';
    case Compact = 'compact';
}
