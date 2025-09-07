<?php

namespace BezhanSalleh\FilamentExceptions\Resources;

use Filament\Panel;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages\ListExceptions;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages\ViewException;
use BezhanSalleh\FilamentExceptions\FilamentExceptions;
use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ExceptionResource extends Resource
{
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Heading')
                    ->activeTab(static fn (): int => static::getPlugin()->getActiveTab())
                    ->tabs([
                        Tab::make('Exception')
                            ->label(static fn (): string => static::getPlugin()->getExceptionTabLabel())
                            ->icon(static fn (): string => static::getPlugin()->getExceptionTabIcon())
                            ->schema([
                                View::make('filament-exceptions::exception'),
                            ]),
                        Tab::make('Headers')
                            ->label(static fn (): string => static::getPlugin()->getHeadersTabLabel())
                            ->icon(static fn (): string => static::getPlugin()->getHeadersTabIcon())
                            ->schema([
                                View::make('filament-exceptions::headers'),
                            ])->columns(1),
                        Tab::make('Cookies')
                            ->label(static fn (): string => static::getPlugin()->getCookiesTabLabel())
                            ->icon(static fn (): string => static::getPlugin()->getCookiesTabIcon())
                            ->schema([
                                View::make('filament-exceptions::cookies'),
                            ]),
                        Tab::make('Body')
                            ->label(static fn (): string => static::getPlugin()->getBodyTabLabel())
                            ->icon(static fn (): string => static::getPlugin()->getBodyTabIcon())
                            ->schema([
                                View::make('filament-exceptions::body'),
                            ]),
                        Tab::make('Queries')
                            ->label(static fn (): string => static::getPlugin()->getQueriesTabLabel())
                            ->icon(static fn (): string => static::getPlugin()->getQueriesTabIcon())
                            ->badge(static fn ($record): string => collect(json_decode($record->query, true, 512, JSON_THROW_ON_ERROR))->count())
                            ->schema([
                                View::make('filament-exceptions::query'),
                            ]),

                    ]),
            ])->columns(1);
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
}
