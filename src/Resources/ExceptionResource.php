<?php

namespace BezhanSalleh\FilamentExceptions\Resources;

use BezhanSalleh\FilamentAddons\Forms\Components;
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

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationLabel = 'Exceptions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Pills::make('Heading')
                    ->activePill(2)
                    ->pills([
                        Components\Pills\Pill::make('Exception')
                            ->icon('heroicon-o-chip')
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::exception'),
                            ]),
                        Components\Pills\Pill::make('Headers')
                            ->icon('heroicon-o-switch-horizontal')
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::headers'),
                            ])->columns(1),
                        Components\Pills\Pill::make('Cookies')
                                ->icon('heroicon-o-database')
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::cookies'),
                            ]),
                        Components\Pills\Pill::make('Body')
                            ->icon('heroicon-s-code')
                            ->schema([
                                Forms\Components\View::make('filament-exceptions::body'),
                            ]),
                        Components\Pills\Pill::make('Query')
                            ->icon('heroicon-s-database')
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
                Tables\Columns\TextColumn::make('path'),
                Tables\Columns\TextColumn::make('method'),
                Tables\Columns\BadgeColumn::make('method')
                    ->colors([
                        'primary',
                        'success' => fn ($state): bool => $state === 'GET',
                        'primary' => fn ($state): bool => $state === 'POST',
                        'warning' => fn ($state): bool => $state === 'PUT',
                        'danger' => fn ($state): bool => $state === 'DELETE',
                        'secondary' => fn ($state): bool => $state === 'PATCH',
                        'gray' => fn ($state): bool => $state === 'OPTIONS',

                    ]),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\BadgeColumn::make('ip')
                    ->extraAttributes(['class' => 'font-mono']),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Occured At')
                    ->sortable()
                    ->searchable()
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'create' => Pages\CreateException::route('/create'),
            'view' => Pages\ViewException::route('/{record}'),
            'edit' => Pages\EditException::route('/{record}/edit'),
        ];
    }
}
