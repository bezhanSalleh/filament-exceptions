<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Resources;

use BezhanSalleh\FilamentExceptions\FilamentExceptions;
use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages\ListExceptions;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages\ViewException;
use BezhanSalleh\FilamentExceptions\Trace\Parser;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Phiki\Grammar\Grammar;
use Phiki\Theme\Theme;

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
        return static::getPlugin()->getNavigationLabel();
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
            ? (string) static::getEloquentQuery()->count()
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
                        'warning' => fn ($state): bool => in_array($state, ['PUT', 'PATCH'], true),
                        'danger' => fn ($state): bool => $state === 'DELETE',
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
                        ->schema([
                            View::make('filament-exceptions::exception'),
                            // Tabs::make('Frames')
                            //     ->extraAttributes(['class' => '[&_.fi-vertical]:overflow-y-auto [&_.fi-vertical]:overscroll-contain [&_.fi-vertical]:h-[32rem]'])
                            //     ->tabs(fn (Model $record): array => static::getFrameTabs($record))
                            //     ->vertical()
                        ]),
                    Tab::make('Headers')
                        ->label(static fn (): string => static::getPlugin()->getHeadersTabLabel())
                        ->icon(static fn (): string => static::getPlugin()->getHeadersTabIcon())
                        ->hidden(fn (Model $record): bool => blank($record->headers))
                        ->schema([
                            Infolists\Components\KeyValueEntry::make('headers')
                                ->hiddenLabel(),
                        ]),
                    Tab::make('Cookies')
                        ->label(static fn (): string => static::getPlugin()->getCookiesTabLabel())
                        ->icon(static fn (): string => static::getPlugin()->getCookiesTabIcon())
                        ->hidden(fn (Model $record): bool => blank($record->cookies))
                        ->schema([
                            Infolists\Components\KeyValueEntry::make('cookies')
                                ->hiddenLabel(),
                        ]),
                    Tab::make('Body')
                        ->label(static fn (): string => static::getPlugin()->getBodyTabLabel())
                        ->icon(static fn (): string => static::getPlugin()->getBodyTabIcon())
                        ->hidden(fn (Model $record): bool => blank($record->body))
                        ->schema([
                            Infolists\Components\KeyValueEntry::make('body')
                                ->hiddenLabel(),
                        ]),
                    Tab::make('Queries')
                        ->label(static fn (): string => static::getPlugin()->getQueriesTabLabel())
                        ->icon(static fn (): string => static::getPlugin()->getQueriesTabIcon())
                        ->badge(static fn ($record): int => collect($record->query)->count())
                        ->hidden(fn (Model $record): bool => blank($record->query))
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
                                        ->extraAttributes([
                                            'style' => '--first-col-width: 25%; --last-col-width: 75%;',
                                            'class' => '[&_tbody_tr_td:first-child]:[width:var(--first-col-width)!important] [&_tbody_tr>td:last-child]:[width:var(--last-col-width)!important]',
                                        ])
                                        ->hiddenLabel()
                                        ->keyLabel('#Bindings: Key')
                                        ->valueLabel('Value')
                                        ->hidden(fn ($state): bool => blank($state)),
                                ])
                                ->contained(false),
                        ]),

                ]),
        ])->columns(1);
    }

    public static function getTraceFrames(Model $record): ?array
    {
        // @phpstan-ignore-next-line
        if (blank(static::$cachedFrames) && $record) {
            $trace = "#0 {$record->file}({$record->line})\n";
            $frames = new Parser(sprintf('%s %s', $trace, $record->trace))->parse();
            array_pop($frames);

            return $frames;
        }

        return static::$cachedFrames;
    }

    public static function getFrameTabs(Model $record): array
    {
        return collect(static::getTraceFrames($record))
            ->filter(fn ($frame): bool => filled($frame) && intval($frame->line()) > 0)
            ->map(fn ($frame, string $index): Tab => Tab::make(str($frame->method())->append($frame->line())->toString())
                ->label(str($frame->file())->replace(base_path() . '/', '')->afterLast('/')->append($frame->method())->afterLast('/')->append(' at line: ' . $frame->line())->toString())
                ->badge(fn () => $frame->line())
                ->schema([
                    CustomCodeEntry::make('frame_' . $index)
                        ->extraAttributes(['class' => '[&_.line]:text-balance'])
                        ->hiddenLabel()
                        ->state($frame->getCodeBlock()->codeString())
                        ->grammar(Grammar::Php)
                        ->lightTheme(Theme::GithubLight)
                        ->darkTheme(Theme::GithubDarkDefault)
                        ->focusLine(intval($frame->line()))
                        ->startLine(1),
                ]))
            ->toArray();
    }
}
