<?php

namespace App\Filament\Personal\Resources\HolidayResource\Pages;

use App\Filament\Personal\Resources\HolidayResource;
use App\Mail\HolidayPending;
use App\Models\Calendar;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ListHolidays extends ListRecords
{
    protected static string $resource = HolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->closeModalByClickingAway(false)
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = Auth::id();
                    $data['calendar_id'] = Calendar::active()->id;
                    $data['status'] = 'pending';
                    return $data;
                })
                ->after(function ($record) {

                    $user = User::find(Auth::id());
                    $userAdmin = User::find(1);

                    $dataToSend = [
                        'name' => $user->name,
                        'email' => $user->email,
                        'type' => $record->type,
                        'start_date' => $record->start_date,
                        'end_date' => $record->end_date
                    ];

                    Mail::to($userAdmin)->send(new HolidayPending($dataToSend));

                    Notification::make()
                        ->title('Your Holiday Request will be Reviewed soon')
                        ->info()
                        ->send();
                })
        ];
    }
}
