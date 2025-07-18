<?php

namespace App\Filament\Resources\TimesheetResource\Pages;

use App\Filament\Resources\TimesheetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('info')
            ->label('This Week Timesheets Filtered by Default')
            ->color('gray')
            ->disabled(),
            Actions\CreateAction::make(),
        ];
    }
}
