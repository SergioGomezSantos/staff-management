<?php

namespace App\Filament\Personal\Resources;

use App\Filament\Personal\Resources\HolidayResource\Pages;
use App\Filament\Personal\Resources\HolidayResource\RelationManagers;
use App\Models\Holiday;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class HolidayResource extends Resource
{
    protected static ?string $model = Holiday::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = parent::getEloquentQuery()->where('user_id', Auth::id())->where('status', 'pending')->count();
        return $pendingCount > 0 ? $pendingCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id())->where('status', 'pending')->count() > 0 ? 'primary' : 'gray';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pending';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->columns(3)
                    ->schema([
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
            ->defaultSort('created_at', 'desc')
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
                Tables\Actions\EditAction::make()
                    ->visible(fn(Holiday $record): bool => $record->status === 'pending')
                    ->after(function ($record) {

                        $user = User::find($record->user_id);
                        $userAdmin = User::find(1);

                        if ($userAdmin) {
                            $dataToSend = [
                                'name' => $user->name,
                                'email' => $user->email,
                                'type' => $record->type,
                                'start_date' => $record->start_date,
                                'end_date' => $record->end_date,
                                'is_edit' => true
                            ];

                            Mail::to($userAdmin)->send(new \App\Mail\HolidayPending($dataToSend));

                            Notification::make()
                                ->title('Your Holiday Request will be Reviewed soon')
                                ->info()
                                ->send();
                        }
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn(Holiday $record): bool => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            $records->filter(fn($record) => $record->status === 'pending')
                                ->each(fn($record) => $record->delete());
                        }),
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
            // 'create' => Pages\CreateHoliday::route('/create'),
            // 'edit' => Pages\EditHoliday::route('/{record}/edit'),
        ];
    }
}
