<?php

namespace BezhanSalleh\FilamentExceptions;

use Illuminate\Http\Request;
use Filament\Support\Assets\Js;
use Filament\Support\Assets\Css;
use Illuminate\Support\Facades\Cache;
use Spatie\LaravelPackageTools\Package;
use Phiki\Adapters\Laravel\Facades\Phiki;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Console\Scheduling\Schedule;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use BezhanSalleh\FilamentExceptions\Commands\InstallCommand;

class FilamentExceptionsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-exceptions')
            ->hasViews()
            ->hasTranslations()
            ->hasMigration('create_filament_exceptions_table')
            ->hasCommand(InstallCommand::class);
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

        // Phiki::cache(Cache::store('file'));

        // FilamentAsset::register([
        //     Js::make('filament-exceptions', __DIR__ . '/../resources/dist/filament-exceptions.js'),
        //     Css::make('filament-exceptions', __DIR__ . '/../resources/dist/filament-exceptions.css'),
        // ], 'bezhansalleh/filament-exceptions');

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('model:prune', [
                '--model' => [FilamentExceptions::getModel()],
            ])->daily();
        });
    }
}
