<?php

namespace BezhanSalleh\FilamentExceptions;

use BezhanSalleh\FilamentExceptions\Commands\MakeExceptionsInstallCommand;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentExceptionsServiceProvider extends PackageServiceProvider
{
    protected array $resources = [
        ExceptionResource::class,
    ];

    protected array $styles = [
        'filament-exceptions-styles' => __DIR__.'/../resources/dist/filament-exceptions.css',
    ];

    protected array $scripts = [
        'filament-exceptions-scripts' => __DIR__.'/../resources/dist/prism.js',
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-exceptions')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_filament_exceptions_table')
            ->hasCommand(MakeExceptionsInstallCommand::class);
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->scoped('filament-exceptions', function ($app): FilamentExceptions {
            return new FilamentExceptions($app->make(Request::class));
        });
    }
}
