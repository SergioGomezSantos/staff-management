<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalEmployees = User::all()->count();
        $totalDepartments = Department::all()->count();

        $todayStart = Carbon::now()->startOfDay();
        $todayEnd = Carbon::now()->endOfDay();

        $incompleteTimesheets = Timesheet::whereNull('end_time')
            ->where('start_time', '<', $todayStart)
            ->orderBy('start_time', 'desc')
            ->count();

        $pendingHolidays = Holiday::where('status', 'pending')->count();

        $holidaysColor = $pendingHolidays > 0 ? 'color: rgb(245 158 11)' : '';
        $timesheetsColor = $incompleteTimesheets > 0 ? 'color: rgb(245 158 11)' : '';

        return [
            Stat::make('Employees', $totalEmployees),
            Stat::make('Departments', $totalDepartments),
                        Stat::make('Past Incomplete Timesheets', $incompleteTimesheets)
                ->value(new HtmlString(
                    '<span style="' . $timesheetsColor .'">' . $incompleteTimesheets . '</span>'
                )),
            Stat::make('Pending Holidays', $pendingHolidays)
                ->value(new HtmlString(
                    '<span style="' . $holidaysColor .'">' . $pendingHolidays . '</span>'
                )),
        ];
    }
}
