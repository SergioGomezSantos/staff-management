<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class UserChart extends ChartWidget
{
    protected static ?string $heading = 'Employees Created';

    protected static ?int $sort = 4;
    public ?string $filter = 'year';

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

            $userCount = match ($unit) {
                'hours' => $this->getUserCountForHour($period),
                'days' => $this->getUserCountForDay($period),
                'months' => $this->getUserCountForMonth($period),
            };

            $data[] = $userCount;
        }

        return [$labels, $data];
    }

    private function getUserCountForHour(Carbon $hour): int
    {
        return User::whereBetween('created_at', [
            $hour->copy()->startOfHour(),
            $hour->copy()->endOfHour()
        ])->count();
    }

    private function getUserCountForDay(Carbon $day): int
    {
        return User::whereDate('created_at', $day->toDateString())->count();
    }

    private function getUserCountForMonth(Carbon $month): int
    {
        return User::whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->count();
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last 7 days',
            'month' => 'Last 30 days',
            'year' => 'Last 12 months',
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