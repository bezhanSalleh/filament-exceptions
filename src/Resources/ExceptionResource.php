<?php

namespace BezhanSalleh\FilamentExceptions\Resources;

use Filament\Forms;
use Filament\Panel;
use Filament\Tables;
use Phiki\Theme\Theme;
use Filament\Infolists;
use Filament\Tables\Table;
use Phiki\Grammar\Grammar;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\IconSize;
use Filament\Actions\BulkActionGroup;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\View;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\CodeEntry;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Infolists\Components\KeyValueEntry;
use BezhanSalleh\FilamentExceptions\Trace\Parser;
use Illuminate\Contracts\Database\Eloquent\Builder;
use BezhanSalleh\FilamentExceptions\FilamentExceptions;
use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;
use BezhanSalleh\FilamentExceptions\Resources\CustomCodeEntry;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages\ViewException;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages\ListExceptions;

class ExceptionResource extends Resource
{
    public static ?array $cachedFrames = null;

    public static function getCluster(): ?string
    {
        return FilamentExceptions::getCluster();
    }

    public static function getPlugin(): FilamentExceptionsPlugin
    {
        return FilamentExceptionsPlugin::get();
    }

    public static function getModel(): string
    {
        return FilamentExceptions::getModel();
    }

    public static function getModelLabel(): string
    {
        return static::getPlugin()->getModelLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return static::getPlugin()->getPluralModelLabel();
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return static::getPlugin()->getActiveNavigationIcon();
    }

    public static function getNavigationGroup(): ?string
    {
        return static::getPlugin()->getNavigationGroup() ?? static::getTitleCasePluralModelLabel();
    }

    public static function getNavigationLabel(): string
    {
        return static::getPlugin()->getNavigationLabel() ?? __('filament-exceptions::filament-exceptions.labels.navigation');
    }

    public static function getNavigationIcon(): string
    {
        return static::getPlugin()->getNavigationIcon();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return static::getPlugin()->getSlug() ?? parent::getSlug();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getPlugin()->shouldEnableNavigationBadge()
            ? static::getEloquentQuery()->count()
            : null;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return filled(FilamentExceptions::getCluster()) || static::getPlugin()->shouldRegisterNavigation();
    }

    public static function getNavigationSort(): ?int
    {
        return static::getPlugin()->getNavigationSort();
    }

    public static function isScopedToTenant(): bool
    {
        return static::getPlugin()->isScopedToTenant();
    }

    public static function getTenantRelationshipName(): string
    {
        return static::getPlugin()->getTenantRelationshipName() ?? parent::getTenantRelationshipName();
    }

    public static function getTenantOwnershipRelationshipName(): string
    {
        return static::getPlugin()->getTenantOwnershipRelationshipName() ?? parent::getTenantOwnershipRelationshipName();
    }

    public static function canGloballySearch(): bool
    {
        return static::getPlugin()->canGloballySearch() && parent::canGloballySearch();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->select('id', 'path', 'method', 'type', 'code', 'ip', 'created_at'))
            ->columns([
                TextColumn::make('method')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.method'))
                    ->badge()
                    ->colors([
                        'gray',
                        'success' => fn ($state): bool => $state === 'GET',
                        'primary' => fn ($state): bool => $state === 'POST',
                        'warning' => fn ($state): bool => $state === 'PUT',
                        'danger' => fn ($state): bool => $state === 'DELETE',
                        'warning' => fn ($state): bool => $state === 'PATCH',
                        'gray' => fn ($state): bool => $state === 'OPTIONS',

                    ])
                    ->searchable()
                    ->sortable(),
                TextColumn::make('path')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.path'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.type'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('code')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.code'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('ip')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.ip'))
                    ->badge()
                    ->extraAttributes(['class' => 'font-mono'])
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.occurred_at'))
                    ->sortable()
                    ->searchable()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->color('primary'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExceptions::route('/'),
            'view' => ViewException::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
                Tabs::make('Heading')
                    ->activeTab(static fn (): int => static::getPlugin()->getActiveTab())
                    ->tabs([
                        Tab::make('Exception')
                            ->label(static fn (): string => static::getPlugin()->getExceptionTabLabel())
                            ->icon(static fn (): string => static::getPlugin()->getExceptionTabIcon())
                            ->schema(function(Model $record) {
                                return collect(static::getTraceFrames($record))->map(function ($frame, $index) {
                                    return
    Section::make(fn (): HtmlString => static::getCustomCodeEntrySection($frame))
        ->id("frame_section_{$index}")
        ->schema([
            CustomCodeEntry::make("frame.{$index}")
                ->hiddenLabel()
                ->state(fn () => $frame->getCodeBlock()->codeString())
                ->grammar(Grammar::Php)
                ->lightTheme(Theme::GithubLight)
                ->darkTheme(Theme::GithubDarkDefault)
                ->focusLine($frame->line())
        ])
        ->extraAlpineAttributes([
            'x-on:expand-section.window' => 'console.log(`yello`, $event)'
        ])
        ->collapsible()
        ->collapsed($index !== 0)
        ->persistCollapsed('frame_section_' . $index);

                                })->toArray();

                            })
                            // ->schema([
                            //     View::make('filament-exceptions::exception'),
                            // ])
                            ,
                        Tab::make('Headers')
                            ->label(static fn (): string => static::getPlugin()->getHeadersTabLabel())
                            ->icon(static fn (): string => static::getPlugin()->getHeadersTabIcon())
                            ->hidden(fn(Model $record) => blank($record->headers))
                            ->schema([
                                Infolists\Components\KeyValueEntry::make('headers')
                                    ->hiddenLabel()
                            ]),
                        Tab::make('Cookies')
                            ->label(static fn (): string => static::getPlugin()->getCookiesTabLabel())
                            ->icon(static fn (): string => static::getPlugin()->getCookiesTabIcon())
                            ->hidden(fn(Model $record) => blank($record->cookies))
                            ->schema([
                                Infolists\Components\KeyValueEntry::make('cookies')
                                    ->hiddenLabel(),
                            ]),
                        Tab::make('Body')
                            ->label(static fn (): string => static::getPlugin()->getBodyTabLabel())
                            ->icon(static fn (): string => static::getPlugin()->getBodyTabIcon())
                            ->hidden(fn(Model $record) => blank($record->body))
                            ->schema([
                                Infolists\Components\KeyValueEntry::make('body')
                                    ->hiddenLabel(),
                            ]),
                        Tab::make('Queries')
                            ->label(static fn (): string => static::getPlugin()->getQueriesTabLabel())
                            ->icon(static fn (): string => static::getPlugin()->getQueriesTabIcon())
                            ->badge(static fn ($record): string => collect($record->query)->count())
                            ->hidden(fn(Model $record) => blank($record->queury))
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('query')
                                    ->hiddenLabel()
                                    ->schema([
                                        CodeEntry::make('sql')
                                            ->hiddenLabel()
                                            ->grammar(Grammar::Sql)
                                            ->lightTheme(Theme::GithubLight)
                                            ->darkTheme(Theme::GithubDarkDefault)
                                            ->copyable()
                                            ->copyMessage('Copied!')
                                            ->copyMessageDuration(1500),
                                        KeyValueEntry::make('bindings')
                                            ->hiddenLabel()
                                            ->keyLabel('#Bindings: Key')
                                            ->valueLabel('Value')
                                            ->hidden(fn($state) => blank($state))
                                    ])
                                    ->contained(false)
                            ]),

                    ]),
            ])->columns(1);
    }


    public static function getTraceFrames(Model $record): ?array
    {
        if (blank(static::$cachedFrames) && $record) {
            $trace = "#0 {$record->file}({$record->line})\n";
            $frames = (new Parser($trace . $record->trace))->parse();
            array_pop($frames);
            static::$cachedFrames = $frames;
        }

        return static::$cachedFrames;
    }

    public static function getCustomCodeEntrySection($frame): HtmlString
    {
        $title = $frame->file() ? str($frame->file())->replace(base_path() . '/', '') : '[internal]';
        return new HtmlString(
            <<<HTML
                <span class="px-4 py-3 font-mono text-left break-words">
                    {$title}
                    in {$frame->method()}
                    at line
                    <span class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl whitespace-normal text-primary-600 bg-primary-500/10 dark:text-primary-500">
                        {$frame->line()}
                    </span>
                </span>
            HTML
        );
    }
}
