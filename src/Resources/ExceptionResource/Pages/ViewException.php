<?php

namespace BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use BezhanSalleh\FilamentExceptions\Trace\Parser;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource;

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

    /**
     * @return array<string>
     */
    public function getPageClasses(): array
    {
        return [
            'fi-resource-view-record-page',
            'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug(Filament::getCurrentOrDefaultPanel())),
            "fi-resource-record-{$this->getRecord()->getKey()}",
        ];
    }
}
