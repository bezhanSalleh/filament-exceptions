<?php

namespace BezhanSalleh\ExceptionPlugin\Resources\ExceptionResource\Pages;

use BezhanSalleh\ExceptionPlugin\Resources\ExceptionResource;
use BezhanSalleh\ExceptionPlugin\Trace\Parser;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewException extends ViewRecord
{
    protected static string $resource = ExceptionResource::class;

    protected static string $view = 'filament-exceptions::view-exception';

    public function getFramesProperty()
    {
        $trace = "#0 {$this->record->file}({$this->record->line})\n";
        $frames = (new Parser($trace . $this->record->trace))->parse();
        array_pop($frames);

        return $frames;
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
