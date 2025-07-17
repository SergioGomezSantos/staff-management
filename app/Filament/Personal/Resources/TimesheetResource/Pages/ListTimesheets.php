<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use App\Models\Calendar;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('info')
                ->label('This Week Timesheets Filtered by Default')
                ->color('gray')
                ->disabled(),

            Action::make('working')
                ->label('Start to Work')
                ->color('success')
                ->visible(fn() => !Timesheet::where('user_id', Auth::id())
                    ->whereNull('end_time')
                    ->exists())
                ->requiresConfirmation()
                ->action(function () {

                    Timesheet::create([
                        'calendar_id' => Calendar::active()->id,
                        'user_id' => Auth::id(),
                        'type' => 'work',
                        'start_time' => Carbon::now(),
                    ]);

                    Notification::make()
                        ->title('Work Started')
                        ->success()
                        ->send();
                }),

            Action::make('stopWorking')
                ->label('Stop Working')
                ->color('danger')
                ->visible(fn() => Timesheet::where('user_id', Auth::id())
                    ->where('type', 'work')
                    ->whereNull('end_time')
                    ->exists())
                ->requiresConfirmation()
                ->action(function () {

                    Timesheet::where('user_id', Auth::id())
                        ->where('type', 'work')
                        ->whereNull('end_time')
                        ->latest()
                        ->first()
                        ->update(['end_time' => Carbon::now()]);

                    Notification::make()
                        ->title('Work Stopped')
                        ->success()
                        ->send();
                }),


            Action::make('inPause')
                ->label('Start Pause')
                ->color('info')
                ->visible(fn() => Timesheet::where('user_id', Auth::id())
                    ->where('type', 'work')
                    ->whereNull('end_time')
                    ->exists())
                ->requiresConfirmation()
                ->action(function () {

                    Timesheet::where('user_id', Auth::id())
                        ->where('type', 'work')
                        ->whereNull('end_time')
                        ->latest()
                        ->first()
                        ->update(['end_time' => Carbon::now()]);

                    Timesheet::create([
                        'calendar_id' => Calendar::active()->id,
                        'user_id' => Auth::id(),
                        'type' => 'pause',
                        'start_time' => Carbon::now(),
                    ]);

                    Notification::make()
                        ->title('Pause Started')
                        ->success()
                        ->send();
                }),

            Action::make('stopInPause')
                ->label('Stop Pause')
                ->color('danger')
                ->visible(fn() => Timesheet::where('user_id', Auth::id())
                    ->where('type', 'pause')
                    ->whereNull('end_time')
                    ->exists())
                ->requiresConfirmation()
                ->action(function () {

                    Timesheet::where('user_id', Auth::id())
                        ->where('type', 'pause')
                        ->whereNull('end_time')
                        ->latest()
                        ->first()
                        ->update(['end_time' => Carbon::now()]);

                    Timesheet::create([
                        'calendar_id' => Calendar::active()->id,
                        'user_id' => Auth::id(),
                        'type' => 'work',
                        'start_time' => Carbon::now(),
                    ]);

                    Notification::make()
                        ->title('Pause Ended')
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make()
                ->closeModalByClickingAway(false)
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = Auth::id();
                    $data['calendar_id'] = Calendar::active()->id;
                    return $data;
                }),
        ];
    }
}
