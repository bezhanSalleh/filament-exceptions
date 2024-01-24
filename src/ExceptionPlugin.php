<?php

declare(strict_types=1);

namespace BezhanSalleh\ExceptionPlugin;

use Filament\Contracts\Plugin;
use Filament\Panel;

class ExceptionPlugin implements Plugin
{
    use Concerns\HasLabels;
    use Concerns\HasNavigation;
    use Concerns\HasTenantScope;
    use Concerns\HasModelPruneInterval;
    use Concerns\HasTabs;

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

    public function boot(Panel $panel): void
    {
        //
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
