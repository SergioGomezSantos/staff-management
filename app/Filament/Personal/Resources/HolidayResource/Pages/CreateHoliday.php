<?php

namespace App\Filament\Personal\Resources\HolidayResource\Pages;

use App\Filament\Personal\Resources\HolidayResource;
use App\Mail\HolidayPending;
use App\Models\Calendar;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CreateHoliday extends CreateRecord
{
    protected static string $resource = HolidayResource::class;
}
