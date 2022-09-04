<?php

namespace BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages;

use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource;
use Filament\Resources\Pages\ListRecords;

class ListExceptions extends ListRecords
{
    protected static string $resource = ExceptionResource::class;

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-chip';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Horray! just set back & enjoy 😎';
    }
}
