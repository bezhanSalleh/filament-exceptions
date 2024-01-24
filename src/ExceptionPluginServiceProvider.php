<?php

namespace BezhanSalleh\ExceptionPlugin;

use BezhanSalleh\ExceptionPlugin\Commands\MakeExceptionsInstallCommand;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ExceptionPluginServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-exceptions')
            ->hasViews()
            ->hasTranslations()
            ->hasMigration('create_filament_exceptions_table')
            ->hasCommand(MakeExceptionsInstallCommand::class);
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->scoped('filament-exceptions', function ($app): ExceptionManager {
            return new ExceptionManager($app->make(Request::class));
        });
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        FilamentAsset::register([
            Js::make('filament-exceptions', __DIR__ . '/../resources/dist/filament-exceptions.js'),
            Css::make('filament-exceptions', __DIR__ . '/../resources/dist/filament-exceptions.css'),
        ], 'bezhansalleh/filament-exceptions');

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('model:prune', [
                '--model' => [ExceptionManager::getModel()],
            ])->daily();
        });
    }
}
