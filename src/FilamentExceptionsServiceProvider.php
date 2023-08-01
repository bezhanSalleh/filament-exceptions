<?php

namespace BezhanSalleh\FilamentExceptions;

use BezhanSalleh\FilamentExceptions\Commands\MakeExceptionsInstallCommand;
use BezhanSalleh\FilamentExceptions\Models\Exception;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentExceptionsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-exceptions')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
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

    public function packageBooted(): void
    {
        parent::packageBooted();

        FilamentAsset::register([
            Js::make('filament-exceptions', __DIR__ . '/../resources/dist/filament-exceptions.js'),
            Css::make('filament-exceptions', __DIR__ . '/../resources/dist/filament-exceptions.css'),
        ], 'bezhansalleh/filament-exceptions');

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('model:prune', [
                '--model' => [Exception::class],
            ])->daily();
        });
    }
}
