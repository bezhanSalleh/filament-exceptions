<?php

namespace BezhanSalleh\FilamentExceptions\Support;

class Utils
{
    public static function getSlug(): string
    {
        return (string) config('filament-exceptions.slug');
    }

    public static function getNavigationSort(): int
    {
        return config('filament-exceptions.navigation_sort');
    }

    public static function isNavigationBadgeEnabled(): bool
    {
        return config('filament-exceptions.navigation_badge', true);
    }

    public static function isNavigationGroupEnabled(): bool
    {
        return config('filament-exceptions.navigation_group', true);
    }

    public static function isGloballySearchable(): bool
    {
        return config('filament-exceptions.is_globally_searchable', false);
    }

    public static function getActivePill(): int
    {
        return config('filament-exceptions.active_pill', 1);
    }
}
