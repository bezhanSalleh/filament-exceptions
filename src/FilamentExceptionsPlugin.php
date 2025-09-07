<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions;

use BezhanSalleh\FilamentExceptions\Concerns\HasLabels;
use BezhanSalleh\FilamentExceptions\Concerns\HasModelPruneInterval;
use BezhanSalleh\FilamentExceptions\Concerns\HasNavigation;
use BezhanSalleh\FilamentExceptions\Concerns\HasTabs;
use BezhanSalleh\FilamentExceptions\Concerns\HasTenantScope;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentExceptionsPlugin implements Plugin
{
    use HasLabels;
    use HasModelPruneInterval;
    use HasNavigation;
    use HasTabs;
    use HasTenantScope;
    use EvaluatesClosures;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-exceptions';
    }

    public function register(Panel $panel): void
    {
        if (is_null(FilamentExceptions::getModel())) {
            FilamentExceptions::model('\\BezhanSalleh\\FilamentExceptions\\Models\\Exception');
        }

        $panel
            ->resources([
                ExceptionResource::class,
            ]);

    }

    public function boot(Panel $panel): void {}

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
