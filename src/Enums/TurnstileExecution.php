<?php

declare(strict_types=1);

namespace l3aro\FilamentTurnstile\Enums;

enum TurnstileExecution: string
{
    case Render = 'render';
    case Execute = 'execute';
}
