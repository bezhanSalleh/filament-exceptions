<?php

namespace BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages;

use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditException extends EditRecord
{
    protected static string $resource = ExceptionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
