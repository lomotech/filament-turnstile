<?php

declare(strict_types=1);

namespace l3aro\FilamentTurnstile\Enums;

enum TurnstileRefreshStrategy: string
{
    case Auto = 'auto';
    case Manual = 'manual';
    case Never = 'never';
}
