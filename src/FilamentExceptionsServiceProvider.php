<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions;

use BezhanSalleh\FilamentExceptions\Commands\InstallCommand;
use BezhanSalleh\FilamentExceptions\QueryRecorder\QueryRecorder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Throwable;

class FilamentExceptionsServiceProvider extends PackageServiceProvider
{
    /**
     * The path to the renderer's distribution files.
     */
    protected const DIST = __DIR__ . '/../dist/';

    /**
     * Get the exception renderer's CSS content.
     */
    public static function css(): string
    {
        return '<style>' . file_get_contents(self::DIST . 'styles.css') . '</style>';
    }

    /**
     * Get the exception renderer's JavaScript content.
     */
    public static function js(): string
    {
        return '<script type="module">' . file_get_contents(self::DIST . 'scripts.js') . '</script>';
    }

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

        $this->app->scoped('filament-exceptions', fn ($app): FilamentExceptions => new FilamentExceptions($app->make(Request::class)));

        $this->app->singleton(QueryRecorder::class, fn ($app) => (new QueryRecorder($app))->start());
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        // Register Laravel's exception renderer components so we can use them
        $this->loadViewsFrom(
            base_path('vendor/laravel/framework/src/Illuminate/Foundation/resources/exceptions/renderer'),
            'laravel-exceptions-renderer'
        );

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
