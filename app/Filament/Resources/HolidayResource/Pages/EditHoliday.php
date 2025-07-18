<?php

namespace App\Filament\Resources\HolidayResource\Pages;

use App\Filament\Resources\HolidayResource;
use App\Mail\HolidayStatusNotification;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form; // Añade esta importación
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class EditHoliday extends EditRecord
{
    protected static string $resource = HolidayResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->relationship('user', 'name')
                ->required(),
            Select::make('calendar_id')
                ->relationship('calendar', 'name')
                ->required(),
            DatePicker::make('start_date')
                ->required(),
            DatePicker::make('end_date'),
            Select::make('type')
                ->options([
                    'vacation' => 'Vacation',
                    'sick_leave' => 'Sick Leave',
                    'personal' => 'Personal',
                    'other' => 'Other',
                ])
                ->required(),
            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'declined' => 'Declined',
                ])
                ->required()
                ->live(),
            Textarea::make('comments')
                ->visible(
                    fn(Get $get): bool =>
                    in_array($get('status'), ['approved', 'declined'])
                )
                ->required(
                    fn(Get $get): bool =>
                    in_array($get('status'), ['declined'])
                ),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if ($data['status'] === 'pending') {
            $data['comments'] = null;
        }
        
        $record->update($data);

        if ($record->status !== 'pending') {
            $user = User::find($data['user_id']);
            $dataToSend = [
                'status' => $data['status'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'comments' => $data['comments'] ?? null,
                'rejection_reason' => $data['status'] === 'declined' ? ($data['comments'] ?? 'No reason provided') : null,
            ];

            Mail::to($user)->send(new HolidayStatusNotification($dataToSend));

            Notification::make()
                ->title('Your Holiday Request has been Reviewed')
                ->sendToDatabase($user);
        }

        return $record;
    }
}
