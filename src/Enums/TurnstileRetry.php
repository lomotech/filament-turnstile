<?php

declare(strict_types=1);

namespace l3aro\FilamentTurnstile\Enums;

enum TurnstileRetry: string
{
    case Auto = 'auto';
    case Never = 'never';
}
