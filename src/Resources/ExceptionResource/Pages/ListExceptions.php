<?php

namespace BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages;

use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource;
use Filament\Resources\Pages\ListRecords;

class ListExceptions extends ListRecords
{
    protected static string $resource = ExceptionResource::class;

    protected function getTableEmptyStateIcon(): ?string
    {
        return config('filament-exceptions.icons.exception');
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return __('filament-exceptions::filament-exceptions.empty_list');
    }
}
