<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions;

use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentExceptionsPlugin implements Plugin
{
    use Concerns\HasLabels;
    use Concerns\HasModelPruneInterval;
    use Concerns\HasNavigation;
    use Concerns\HasTabs;
    use Concerns\HasTenantScope;
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
