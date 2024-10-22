<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentExceptionsPlugin implements Plugin
{
    use Concerns\HasLabels;
    use Concerns\HasModelPruneInterval;
    use Concerns\HasNavigation;
    use Concerns\HasTabs;
    use Concerns\HasTenantScope;

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
                Resources\ExceptionResource::class,
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
