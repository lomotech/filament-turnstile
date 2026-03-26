<?php

declare(strict_types=1);

namespace l3aro\FilamentTurnstile;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
class FilamentTurnstileResponse implements Arrayable
{
    public function __construct(
        public readonly bool $success,
        public readonly ?array $errorCodes = null,
        public readonly ?string $challengeTs = null,
        public readonly ?string $hostname = null,
        public readonly ?string $action = null,
        public readonly ?string $cData = null,
    ) {}

    public static function make(bool $success, ?array $errorCodes): self
    {
        return new self(success: $success, errorCodes: $errorCodes);
    }

    public static function fromResponse(
        bool $success,
        ?array $errorCodes = null,
        ?string $challengeTs = null,
        ?string $hostname = null,
        ?string $action = null,
        ?string $cData = null,
    ): self {
        return new self(
            success: $success,
            errorCodes: $errorCodes,
            challengeTs: $challengeTs,
            hostname: $hostname,
            action: $action,
            cData: $cData,
        );
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isExpired(): bool
    {
        return in_array('timeout-or-duplicate', $this->errorCodes ?? [], true);
    }

    public function toArray(): array
    {
        return array_filter([
            'success' => $this->success,
            'errorCodes' => $this->errorCodes,
            'challengeTs' => $this->challengeTs,
            'hostname' => $this->hostname,
            'action' => $this->action,
            'cData' => $this->cData,
        ], fn(mixed $value): bool => $value !== null);
    }
}
