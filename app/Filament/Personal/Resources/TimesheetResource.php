<?php

namespace App\Filament\Personal\Resources;

use App\Filament\Personal\Resources\TimesheetResource\Pages;
use App\Filament\Personal\Resources\TimesheetResource\RelationManagers;
use App\Models\Timesheet;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->columns(3)
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_time')
                            ->required()
                            ->default(function () {
                                $lastEntry = Timesheet::where('user_id', Auth::id())->latest('start_time')->first();
                                return $lastEntry && $lastEntry->end_time ? $lastEntry->end_time : now();
                            })
                            ->seconds(false),
                        Forms\Components\DateTimePicker::make('end_time')
                            ->seconds(false),
                        Forms\Components\Select::make('type')
                            ->options(
                                [
                                    'work' => 'Working',
                                    'pause' => 'In Pause',
                                ]
                            )
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('calendar.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'work' => 'Working',
                        'pause' => 'In Pause',
                        default => $state
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'work' => 'success',
                        'pause' => 'gray',
                        default => 'primary'
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->dateTime('M j, Y H:i')
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('Type')
                    ->options([
                        'work' => 'Working',
                        'pause' => 'In Pause',
                    ]),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('From Date')
                            ->default(now()->startOfWeek()),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Until Date')
                            ->default(now()->endOfWeek()),
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('clear_filters')
                                ->label('Remove Dates')
                                ->color('danger')
                                ->link()
                                ->action(function ($livewire) {
                                    $livewire->removeTableFilters();
                                })
                        ])
                            ->alignment('right')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('start_time', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('start_time', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTimesheets::route('/'),
            // 'create' => Pages\CreateTimesheet::route('/create'),
            // 'edit' => Pages\EditTimesheet::route('/{record}/edit'),
        ];
    }
}
