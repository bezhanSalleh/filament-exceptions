<?php

namespace BezhanSalleh\FilamentExceptions\Resources;

use BezhanSalleh\FilamentAddons\Forms\Components\Pills;
use BezhanSalleh\FilamentAddons\Forms\Components\Pills\Pill;
use BezhanSalleh\FilamentExceptions\Models\Exception;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages;
use BezhanSalleh\FilamentExceptions\Support\Utils;
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
        return __('filament-exceptions::filament-exceptions.model.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-exceptions::filament-exceptions.model.plural_label');
    }

    protected static function getNavigationGroup(): ?string
    {
        return Utils::isNavigationGroupEnabled()
            ? __('filament-exceptions::filament-exceptions.navigation.group')
            : '';
    }

    protected static function getNavigationLabel(): string
    {
        return __('filament-exceptions::filament-exceptions.navigation.label');
    }

    protected static function getNavigationIcon(): string
    {
        return __('filament-exceptions::filament-exceptions.navigation.icon');
    }

    public static function getSlug(): string
    {
        return Utils::getSlug();
    }

    protected static function getNavigationBadge(): ?string
    {
        return Utils::isNavigationBadgeEnabled()
            ? static::$model::count()
            : null;
    }

    public static function canGloballySearch(): bool
    {
        return Utils::isGloballySearchable() && count(static::getGloballySearchableAttributes()) && static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Pills::make('Heading')
                    ->activePill(fn (): int => Utils::getActivePill())
                    ->pills([
                        Pill::make('Exception')
                            ->label(fn (): string => __('filament-exceptions::filament-exceptions.pills.exception.label'))
                            ->icon(fn (): string => __('filament-exceptions::filament-exceptions.pills.exception.icon'))
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::exception'),
                            ]),
                        Pill::make('Headers')
                            ->label(fn (): string => __('filament-exceptions::filament-exceptions.pills.headers.label'))
                            ->icon(fn (): string => __('filament-exceptions::filament-exceptions.pills.headers.icon'))
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::headers'),
                            ])->columns(1),
                        Pill::make('Cookies')
                            ->label(fn (): string => __('filament-exceptions::filament-exceptions.pills.cookies.label'))
                            ->icon(fn (): string => __('filament-exceptions::filament-exceptions.pills.cookies.icon'))
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::cookies'),
                            ]),
                        Pill::make('Body')
                            ->label(fn (): string => __('filament-exceptions::filament-exceptions.pills.body.label'))
                            ->icon(fn (): string => __('filament-exceptions::filament-exceptions.pills.body.icon'))
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::body'),
                            ]),
                        Pill::make('Queries')
                            ->label(fn (): string => __('filament-exceptions::filament-exceptions.pills.queries.label'))
                            ->icon(fn (): string => __('filament-exceptions::filament-exceptions.pills.queries.icon'))
                            ->badge(fn ($record): string => collect(json_decode($record->query, true))->count())
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
