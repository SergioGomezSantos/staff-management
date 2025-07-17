<?php

namespace App\Filament\Widgets;

use App\Models\Timesheet;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TimesheetChart extends ChartWidget
{
    protected static ?string $heading = 'Timesheets';

    protected static ?int $sort = 5;
    public ?string $filter = 'week';

    protected function getData(): array
    {
        $now = Carbon::now();

        [$labels, $data] = match ($this->filter) {
            'today' => $this->getDataForPeriod($now, 'hours', 24, 'H:00'),
            'week' => $this->getDataForPeriod($now, 'days', 7, 'M d'),
            'month' => $this->getDataForPeriod($now, 'days', 30, 'M d'),
            'year' => $this->getDataForPeriod($now, 'months', 12, 'M Y'),
            default => $this->getDataForPeriod($now, 'months', 12, 'M Y'),
        };

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'borderWidth' => 1,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)', // info-500 20% opacity
                    'borderColor' => 'rgb(37, 99, 235)' // info-600
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getDataForPeriod(Carbon $now, string $unit, int $count, string $format): array
    {
        $labels = [];
        $data = [];

        for ($i = $count - 1; $i >= 0; $i--) {
            $period = match ($unit) {
                'hours' => $now->copy()->subHours($i),
                'days' => $now->copy()->subDays($i),
                'months' => $now->copy()->subMonths($i),
            };

            $labels[] = $period->format($format);

            $timesheetCount = match ($unit) {
                'hours' => $this->getTimesheetCountForHour($period),
                'days' => $this->getTimesheetCountForDay($period),
                'months' => $this->getTimesheetCountForMonth($period),
            };

            $data[] = $timesheetCount;
        }

        return [$labels, $data];
    }

    private function getTimesheetCountForHour(Carbon $hour): int
    {
        return Timesheet::whereBetween('start_time', [
            $hour->copy()->startOfHour(),
            $hour->copy()->endOfHour()
        ])->count();
    }

    private function getTimesheetCountForDay(Carbon $day): int
    {
        return Timesheet::whereDate('start_time', $day->toDateString())->count();
    }

    private function getTimesheetCountForMonth(Carbon $month): int
    {
        return Timesheet::whereYear('start_time', $month->year)
            ->whereMonth('start_time', $month->month)
            ->count();
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Last 24 Hours',
            'week' => 'Last 7 Days',
            'month' => 'Last 30 Days',
            'year' => 'Last 12 Months',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}