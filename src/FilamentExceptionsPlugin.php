<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentExceptionsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
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
}
