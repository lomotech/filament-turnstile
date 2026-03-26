<?php

declare(strict_types=1);

namespace l3aro\FilamentTurnstile;

use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use l3aro\FilamentTurnstile\Testing\TestsFilamentTurnstile;

class FilamentTurnstileServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-turnstile';

    public static string $viewNamespace = 'filament-turnstile';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('l3aro/filament-turnstile');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    #[\Override]
    public function packageBooted(): void
    {
        Testable::mixin(new TestsFilamentTurnstile());
    }
}
