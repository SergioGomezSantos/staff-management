<?php

namespace App\Filament\Personal\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        $approvedHolidays = $this->getHolidays($user, 'approved');
        $pendingHolidays = $this->getHolidays($user, 'pending');
        $workHours = $this->getWorkHours($user, 'work');
        $pauseHours = $this->getWorkHours($user, 'pause');

        $approvedColor = $approvedHolidays > 0 ? 'color: rgb(22 163 74)' : '';
        $pendingColor = $pendingHolidays > 0 ? 'color: rgb(245 158 11)' : '';
        $workHoursColor = $this->isMoreThan8Hours($workHours) ? 'color: rgb(2 132 199)' : '';

        return [
            Stat::make('Approved Holidays', $approvedHolidays)
                ->value(new HtmlString(
                    '<span style="' . $approvedColor . '">' . $approvedHolidays . '</span>'
                )),

            Stat::make('Pending Holidays', $pendingHolidays)
                ->value(new HtmlString(
                    '<span style="' . $pendingColor . '">' . $pendingHolidays . '</span>'
                )),

            Stat::make('Today Work Time', $workHours)
                ->value(new HtmlString(
                    '<span style="' . $workHoursColor . '">' . $workHours . '</span>'
                )),
            Stat::make('Today Pause Time', $pauseHours)
        ];
    }

    protected function getHolidays(User $user, $status)
    {
        return Holiday::where('user_id', $user->id)->where('status', $status)->count();
    }

    protected function getWorkHours(User $user, $type)
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type', $type)
            ->where(function ($query) use ($todayStart, $todayEnd) {

                $query->whereBetween('start_time', [$todayStart, $todayEnd])

                    ->orWhereBetween('end_time', [$todayStart, $todayEnd])

                    ->orWhere(function ($subQuery) use ($todayEnd) {
                        $subQuery->where('start_time', '<=', $todayEnd)
                            ->whereNull('end_time');
                    });
            })
            ->get();

        $totalSeconds = 0;

        foreach ($timesheets as $timesheet) {

            $start = Carbon::parse($timesheet->start_time);
            $end = $timesheet->end_time ? Carbon::parse($timesheet->end_time) : now();

            $start = $start->isBefore($todayStart) ? $todayStart : $start;
            $end = $end->isAfter($todayEnd) ? $todayEnd : $end;

            if ($start <= $end) {
                $totalSeconds += $start->diffInSeconds($end);
            }
        }

        return gmdate('H:i', $totalSeconds);
    }

    protected function isMoreThan8Hours(string $time): bool
    {
        if ($time === '00:00') {
            return false;
        }

        try {
            [$hours, $minutes] = explode(':', $time);
            return (int)$hours >= 8;
        } catch (\Exception $e) {
            return false;
        }
    }
}
