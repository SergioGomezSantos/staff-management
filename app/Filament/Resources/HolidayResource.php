<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HolidayResource\Pages;
use App\Filament\Resources\HolidayResource\RelationManagers;
use App\Models\Holiday;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class HolidayResource extends Resource
{
    protected static ?string $model = Holiday::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return parent::getEloquentQuery()->where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return parent::getEloquentQuery()->where('status', 'pending')->count() > 0 ? 'primary' : 'gray';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pending';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name'
                    )
                    ->required(),
                Forms\Components\Select::make('calendar_id')
                    ->relationship(
                        name: 'calendar',
                        titleAttribute: 'name'
                    )
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\Select::make('type')
                    ->options([
                        'vacation' => 'Vacation',
                        'sick_leave' => 'Sick Leave',
                        'personal' => 'Personal',
                        'other' => 'Other',
                    ])
                    ->required()
                    ->default('vacation'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'declined' => 'Declined',
                    ])
                    ->required()
                    ->default('pending'),
            ]);
    }

    public static function getEditFormSchema(): array
    {
        return [
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->required(),
            Forms\Components\Select::make('calendar_id')
                ->relationship('calendar', 'name')
                ->required(),
            Forms\Components\DatePicker::make('start_date')
                ->required(),
            Forms\Components\DatePicker::make('end_date'),
            Forms\Components\Select::make('type')
                ->options([
                    'vacation' => 'Vacation',
                    'sick_leave' => 'Sick Leave',
                    'personal' => 'Personal',
                    'other' => 'Other',
                ])
                ->required(),
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'declined' => 'Declined',
                ])
                ->required()
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    if ($state === 'pending') {
                        $set('comments', null);
                    }
                }),
            Forms\Components\Textarea::make('comments')
                ->visible(function (Forms\Get $get) {
                    return in_array($get('status'), ['approved', 'declined']);
                })
                ->required(function (Forms\Get $get) {
                    return in_array($get('status'), ['approved', 'declined']);
                }),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('calendar.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'vacation' => 'Vacation',
                        'sick_leave' => 'Sick Leave',
                        'personal' => 'Personal',
                        'other' => 'Other',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'vacation' => 'info',
                        'sick_leave' => 'danger',
                        'personal' => 'gray',
                        'other' => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'declined' => 'danger'
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('comments')
                    ->limit(20)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'vacation' => 'Vacation',
                        'sick_leave' => 'Sick Leave',
                        'personal' => 'Personal',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'declined' => 'Declined',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exports([
                        ExcelExport::make('With Names')->fromTable()
                            ->withFilename('Holidays_' . date('Y-m-d') . '_names_export')
                            ->askForWriterType(),
                        ExcelExport::make('With IDs')->fromForm()
                            ->withFilename('Holidays_' . date('Y-m-d') . '_ids_export')
                            ->askForWriterType(),
                    ])
                ]),
            ]);
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
            'index' => Pages\ListHolidays::route('/'),
            'create' => Pages\CreateHoliday::route('/create'),
            'edit' => Pages\EditHoliday::route('/{record}/edit'),
        ];
    }
}
