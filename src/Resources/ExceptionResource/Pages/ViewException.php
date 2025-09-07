<?php

namespace BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages;

use Filament\Actions\DeleteAction;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource;
use BezhanSalleh\FilamentExceptions\Trace\Parser;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewException extends ViewRecord
{
    protected static string $resource = ExceptionResource::class;

    protected string $view = 'filament-exceptions::view-exception';

    public function getFramesProperty(): ?array
    {
        $trace = "#0 {$this->record->file}({$this->record->line})\n";
        $frames = (new Parser($trace . $this->record->trace))->parse();
        array_pop($frames);

        return $frames;
    }

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
