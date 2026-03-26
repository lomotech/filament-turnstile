<?php

declare(strict_types=1);

namespace l3aro\FilamentTurnstile\Forms;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\ArgumentValue;
use l3aro\FilamentTurnstile\Enums\TurnstileAppearance;
use l3aro\FilamentTurnstile\Enums\TurnstileExecution;
use l3aro\FilamentTurnstile\Enums\TurnstileRefreshStrategy;
use l3aro\FilamentTurnstile\Enums\TurnstileRetry;
use l3aro\FilamentTurnstile\Enums\TurnstileSize;
use l3aro\FilamentTurnstile\Enums\TurnstileTheme;
use l3aro\FilamentTurnstile\Facades\FilamentTurnstileFacade;
use l3aro\FilamentTurnstile\Rules\TurnstileRule;

class Turnstile extends Field
{
    use HasAlignment;

    protected string $view = 'filament-turnstile::forms.turnstile';

    protected string $viewIdentifier = 'turnstile';

    protected string|Closure|ArgumentValue|TurnstileTheme|null $theme = ArgumentValue::Default;

    protected string|Closure|ArgumentValue|TurnstileSize|null $size = ArgumentValue::Default;

    protected string|Closure|ArgumentValue|null $language = ArgumentValue::Default;

    protected string|Closure|ArgumentValue|null $resetEvent = ArgumentValue::Default;

    protected string|Closure|ArgumentValue|TurnstileAppearance|null $appearance = ArgumentValue::Default;

    protected string|Closure|ArgumentValue|TurnstileExecution|null $execution = ArgumentValue::Default;

    protected string|Closure|ArgumentValue|TurnstileRetry|null $retry = ArgumentValue::Default;

    protected int|Closure|ArgumentValue|null $retryInterval = ArgumentValue::Default;

    protected string|Closure|ArgumentValue|TurnstileRefreshStrategy|null $refreshExpired = ArgumentValue::Default;

    protected string|Closure|ArgumentValue|TurnstileRefreshStrategy|null $refreshTimeout = ArgumentValue::Default;

    protected string|Closure|null $turnstileAction = null;

    protected string|Closure|null $cData = null;

    protected bool|Closure|ArgumentValue $feedbackEnabled = ArgumentValue::Default;

    protected ?Closure $onExpired = null;

    protected ?Closure $onTimeout = null;

    protected ?Closure $onError = null;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->required()
            ->hiddenLabel()
            ->rule(new TurnstileRule())
            ->dehydrated(false);
    }

    public function theme(string|Closure|ArgumentValue|TurnstileTheme|null $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function size(string|Closure|ArgumentValue|TurnstileSize|null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function language(string|Closure|ArgumentValue|null $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function resetEvent(string|Closure|ArgumentValue|null $resetEvent): static
    {
        $this->resetEvent = $resetEvent;

        return $this;
    }

    public function appearance(string|Closure|ArgumentValue|TurnstileAppearance|null $appearance): static
    {
        $this->appearance = $appearance;

        return $this;
    }

    public function execution(string|Closure|ArgumentValue|TurnstileExecution|null $execution): static
    {
        $this->execution = $execution;

        return $this;
    }

    public function retry(string|Closure|ArgumentValue|TurnstileRetry|null $retry): static
    {
        $this->retry = $retry;

        return $this;
    }

    public function retryInterval(int|Closure|ArgumentValue|null $retryInterval): static
    {
        $this->retryInterval = $retryInterval;

        return $this;
    }

    public function refreshExpired(string|Closure|ArgumentValue|TurnstileRefreshStrategy|null $refreshExpired): static
    {
        $this->refreshExpired = $refreshExpired;

        return $this;
    }

    public function refreshTimeout(string|Closure|ArgumentValue|TurnstileRefreshStrategy|null $refreshTimeout): static
    {
        $this->refreshTimeout = $refreshTimeout;

        return $this;
    }

    public function turnstileAction(string|Closure|null $action): static
    {
        $this->turnstileAction = $action;

        return $this;
    }

    public function cData(string|Closure|null $cData): static
    {
        $this->cData = $cData;

        return $this;
    }

    public function feedbackEnabled(bool|Closure|ArgumentValue $feedbackEnabled = true): static
    {
        $this->feedbackEnabled = $feedbackEnabled;

        return $this;
    }

    public function onExpired(?Closure $callback): static
    {
        $this->onExpired = $callback;

        return $this;
    }

    public function onTimeout(?Closure $callback): static
    {
        $this->onTimeout = $callback;

        return $this;
    }

    public function onError(?Closure $callback): static
    {
        $this->onError = $callback;

        return $this;
    }

    public function getTheme(): ?string
    {
        $theme = $this->evaluate($this->theme);

        if ($theme instanceof \BackedEnum) {
            return $theme->value;
        }

        return $theme === ArgumentValue::Default ? 'auto' : $theme;
    }

    public function getSize(): ?string
    {
        $size = $this->evaluate($this->size);

        if ($size instanceof \BackedEnum) {
            return $size->value;
        }

        return $size === ArgumentValue::Default ? 'normal' : $size;
    }

    public function getLanguage(): ?string
    {
        $language = $this->evaluate($this->language);

        return $language === ArgumentValue::Default ? app()->getLocale() : $language;
    }

    public function getResetEvent(): ?string
    {
        $resetEvent = $this->evaluate($this->resetEvent);

        return $resetEvent === ArgumentValue::Default ? FilamentTurnstileFacade::getResetEventName() : $resetEvent;
    }

    public function getAppearance(): ?string
    {
        $appearance = $this->evaluate($this->appearance);

        if ($appearance instanceof \BackedEnum) {
            return $appearance->value;
        }

        return $appearance === ArgumentValue::Default ? null : $appearance;
    }

    public function getExecution(): ?string
    {
        $execution = $this->evaluate($this->execution);

        if ($execution instanceof \BackedEnum) {
            return $execution->value;
        }

        return $execution === ArgumentValue::Default ? null : $execution;
    }

    public function getRetry(): ?string
    {
        $retry = $this->evaluate($this->retry);

        if ($retry instanceof \BackedEnum) {
            return $retry->value;
        }

        return $retry === ArgumentValue::Default ? null : $retry;
    }

    public function getRetryInterval(): ?int
    {
        $retryInterval = $this->evaluate($this->retryInterval);

        return $retryInterval === ArgumentValue::Default ? null : $retryInterval;
    }

    public function getRefreshExpired(): ?string
    {
        $refreshExpired = $this->evaluate($this->refreshExpired);

        if ($refreshExpired instanceof \BackedEnum) {
            return $refreshExpired->value;
        }

        return $refreshExpired === ArgumentValue::Default ? null : $refreshExpired;
    }

    public function getRefreshTimeout(): ?string
    {
        $refreshTimeout = $this->evaluate($this->refreshTimeout);

        if ($refreshTimeout instanceof \BackedEnum) {
            return $refreshTimeout->value;
        }

        return $refreshTimeout === ArgumentValue::Default ? null : $refreshTimeout;
    }

    public function getTurnstileAction(): ?string
    {
        return $this->evaluate($this->turnstileAction);
    }

    public function getCData(): ?string
    {
        return $this->evaluate($this->cData);
    }

    public function getFeedbackEnabled(): ?bool
    {
        $feedbackEnabled = $this->evaluate($this->feedbackEnabled);

        return $feedbackEnabled === ArgumentValue::Default ? null : $feedbackEnabled;
    }

    public function getAlignmentClasses(): ?string
    {
        return match ($this->getAlignment()) {
            Alignment::Center => 'justify-center',
            Alignment::Left, Alignment::Start => 'justify-start',
            Alignment::Right, Alignment::End => 'justify-end',
            Alignment::Between => 'justify-between',
            default => null,
        };
    }
}
