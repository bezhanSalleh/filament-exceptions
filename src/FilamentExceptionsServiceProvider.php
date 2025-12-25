<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions;

use BezhanSalleh\FilamentExceptions\Commands\InstallCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Throwable;

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

    public function packageBooted(): void
    {
        parent::packageBooted();

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->command('model:prune', [
                '--model' => [FilamentExceptions::getModel()],
            ])->daily();
        });

        $this->callAfterResolving(ExceptionHandler::class, function (ExceptionHandler $handler): void {
            $handler->reportable(function (Throwable $e) use ($handler): void {
                if ($handler->shouldReport($e)) {
                    FilamentExceptions::report($e);
                }
            });
        });
    }
}
