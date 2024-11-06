<?php

namespace BezhanSalleh\FilamentExceptions\Resources;

use BezhanSalleh\FilamentExceptions\Facades\FilamentExceptions;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExceptionResource extends Resource
{
    public static function getModel(): string
    {
        return FilamentExceptions::getModel();
    }

    public static function getModelLabel(): string
    {
        return __('filament-exceptions::filament-exceptions.labels.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-exceptions::filament-exceptions.labels.model_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-exceptions::filament-exceptions.labels.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-exceptions::filament-exceptions.labels.navigation');
    }

    public static function getNavigationIcon(): string
    {
        return config('filament-exceptions.icons.navigation');
    }

    public static function getSlug(): string
    {
        return config('filament-exceptions.slug');
    }

    public static function getNavigationBadge(): ?string
    {
        if (config('filament-exceptions.navigation_badge')) {
            return static::getEloquentQuery()->count();
        }

        return null;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) config('filament-exceptions.navigation_enabled');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-exceptions.navigation_sort');
    }

    public static function isScopedToTenant(): bool
    {
        return config('filament-exceptions.is_scoped_to_tenant', true);
    }

    public static function canGloballySearch(): bool
    {
        return config('filament-exceptions.is_globally_searchable')
            && count(static::getGloballySearchableAttributes())
            && static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Heading')
                    ->activeTab(static fn (): int => config('filament-exceptions.active_tab'))
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Exception')
                            ->label(static fn (): string => __('filament-exceptions::filament-exceptions.labels.tabs.exception'))
                            ->icon(static fn (): string => config('filament-exceptions.icons.exception'))
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::exception'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Headers')
                            ->label(static fn (): string => __('filament-exceptions::filament-exceptions.labels.tabs.headers'))
                            ->icon(static fn (): string => config('filament-exceptions.icons.headers'))
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::headers'),
                            ])->columns(1),
                        Forms\Components\Tabs\Tab::make('Cookies')
                            ->label(static fn (): string => __('filament-exceptions::filament-exceptions.labels.tabs.cookies'))
                            ->icon(static fn (): string => config('filament-exceptions.icons.cookies'))
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::cookies'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Body')
                            ->label(static fn (): string => __('filament-exceptions::filament-exceptions.labels.tabs.body'))
                            ->icon(static fn (): string => config('filament-exceptions.icons.body'))
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::body'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Queries')
                            ->label(static fn (): string => __('filament-exceptions::filament-exceptions.labels.tabs.queries'))
                            ->icon(static fn (): string => config('filament-exceptions.icons.queries'))
                            ->badge(static fn ($record): string => collect(json_decode($record->query, true, 512, JSON_THROW_ON_ERROR))->count())
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::query'),
                            ]),

                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('method')
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
                Tables\Columns\TextColumn::make('path')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.path'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.type'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.code'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('ip')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.ip'))
                    ->badge()
                    ->extraAttributes(['class' => 'font-mono'])
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.occurred_at'))
                    ->sortable()
                    ->searchable()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListExceptions::route('/'),
            'view' => Pages\ViewException::route('/{record}'),
        ];
    }
}
