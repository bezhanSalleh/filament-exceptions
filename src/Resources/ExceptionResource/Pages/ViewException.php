<?php

namespace BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use BezhanSalleh\FilamentExceptions\Trace\Parser;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource;
use Filament\Infolists;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class ViewException extends ViewRecord
{
    protected static string $resource = ExceptionResource::class;

    protected string $view = 'filament-exceptions::view-exception';

    // protected ?array $cachedFrames = null;

    // public function getFramesProperty(): ?array
    // {
    //     if (blank($this->cachedFrames)) {
    //         $trace = "#0 {$this->record->file}({$this->record->line})\n";
    //         $frames = (new Parser($trace . $this->record->trace))->parse();
    //         array_pop($frames);
    //         $this->cachedFrames = $frames;
    //     }

    //     return $this->cachedFrames;
    // }

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

    // public function infolist(Schema $schema): Schema
    // {
    //     return $schema->components([
    //             Tabs::make('Heading')
    //                 // ->activeTab(static fn (): int => static::getPlugin()->getActiveTab())
    //                 ->tabs([
    //                     // Tab::make('Exception')
    //                     //     ->label(static fn (): string => static::getPlugin()->getExceptionTabLabel())
    //                     //     ->icon(static fn (): string => static::getPlugin()->getExceptionTabIcon())
    //                     //     ->schema([
    //                     //         View::make('filament-exceptions::exception'),
    //                     //     ]),
    //                     Tab::make('Headers')
    //                         // ->label(static fn (): string => static::getPlugin()->getHeadersTabLabel())
    //                         // ->icon(static fn (): string => static::getPlugin()->getHeadersTabIcon())
    //                         ->schema([
    //                             Infolists\Components\KeyValueEntry::make('headers')
    //                                 // ->state(fn ($record) => json_decode($record->headers))
    //                                 ->hiddenLabel()
    //                         ])->columns(1),
    //                     Tab::make('Cookies')
    //                         // ->label(static fn (): string => static::getPlugin()->getCookiesTabLabel())
    //                         // ->icon(static fn (): string => static::getPlugin()->getCookiesTabIcon())
    //                         ->schema([
    //                             Infolists\Components\KeyValueEntry::make('cookies'),
    //                         ]),
    //                     // Tab::make('Body')
    //                     //     ->label(static fn (): string => static::getPlugin()->getBodyTabLabel())
    //                     //     ->icon(static fn (): string => static::getPlugin()->getBodyTabIcon())
    //                     //     ->schema([
    //                     //         View::make('filament-exceptions::body'),
    //                     //     ]),
    //                     // Tab::make('Queries')
    //                     //     ->label(static fn (): string => static::getPlugin()->getQueriesTabLabel())
    //                     //     ->icon(static fn (): string => static::getPlugin()->getQueriesTabIcon())
    //                     //     ->badge(static fn ($record): string => collect(json_decode($record->query, true, 512, JSON_THROW_ON_ERROR))->count())
    //                     //     ->schema([
    //                     //         View::make('filament-exceptions::query'),
    //                     //     ]),

    //                 ]),
    //         ])->columns(1);
    // }
}
