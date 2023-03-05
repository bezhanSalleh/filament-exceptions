<?php

namespace BezhanSalleh\FilamentExceptions\Resources;

use BezhanSalleh\FilamentAddons\Forms\Components\Pills;
use BezhanSalleh\FilamentAddons\Forms\Components\Pills\Pill;
use BezhanSalleh\FilamentExceptions\Models\Exception;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ExceptionResource extends Resource
{
    protected static ?string $model = Exception::class;

    public static function getModelLabel(): string
    {
        return __('filament-exceptions::filament-exceptions.labels.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-exceptions::filament-exceptions.labels.model_plural');
    }

    protected static function getNavigationGroup(): ?string
    {
        return config('filament-exceptions.navigation_group');
    }

    protected static function getNavigationLabel(): string
    {
        return __('filament-exceptions::filament-exceptions.labels.navigation');
    }

    protected static function getNavigationIcon(): string
    {
        return config('filament-exceptions::icons.navigation');
    }

    public static function getSlug(): string
    {
        return config('filament-exceptions.slug');
    }

    protected static function getNavigationBadge(): ?string
    {
        if (config('filament-exceptions.navigation_badge')) {
            return static::$model::count();
        }

        return null;
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return (bool) config('filament-exceptions.navigation_enabled');
    }

    protected static function getNavigationSort(): ?int
    {
        return config('filament-exceptions.navigation_sort');
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
                Pills::make('Heading')
                    ->activePill(static fn (): int => config('filament-exceptions.active_pill'))
                    ->pills([
                        Pill::make('Exception')
                            ->label(static fn (): string => __('filament-exceptions::filament-exceptions.labels.pills.exception'))
                            ->icon(static fn (): string => config('filament-exceptions.icons.exception'))
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::exception'),
                            ]),
                        Pill::make('Headers')
                            ->label(static fn (): string => __('filament-exceptions::filament-exceptions.labels.pills..headers'))
                            ->icon(static fn (): string => config('filament-exceptions.icons.headers'))
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::headers'),
                            ])->columns(1),
                        Pill::make('Cookies')
                            ->label(static fn (): string => __('filament-exceptions::filament-exceptions.labels.pills.cookies'))
                            ->icon(static fn (): string => config('filament-exceptions.icons.cookies'))
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::cookies'),
                            ]),
                        Pill::make('Body')
                            ->label(static fn (): string => __('filament-exceptions::filament-exceptions.labels.pills.body'))
                            ->icon(static fn (): string => config('filament-exceptions.icons.body'))
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::body'),
                            ]),
                        Pill::make('Queries')
                            ->label(static fn (): string => __('filament-exceptions::filament-exceptions.labels.pills.queries'))
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
                Tables\Columns\BadgeColumn::make('method')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.method'))
                    ->colors([
                        'primary',
                        'success' => fn ($state): bool => $state === 'GET',
                        'primary' => fn ($state): bool => $state === 'POST',
                        'warning' => fn ($state): bool => $state === 'PUT',
                        'danger' => fn ($state): bool => $state === 'DELETE',
                        'secondary' => fn ($state): bool => $state === 'PATCH',
                        'gray' => fn ($state): bool => $state === 'OPTIONS',

                    ])
                    ->searchable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('path')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.path')),
                Tables\Columns\TextColumn::make('type')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.type'))
                    ->searchable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.code')),
                Tables\Columns\BadgeColumn::make('ip')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.ip'))
                    ->extraAttributes(['class' => 'font-mono'])
                    ->searchable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(fn (): string => __('filament-exceptions::filament-exceptions.columns.occurred_at'))
                    ->sortable()
                    ->searchable()
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
