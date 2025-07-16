<?php

namespace App\Filament\Widgets;

use App\Models\Holiday;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PendingHolidayChart extends ChartWidget
{
    protected static ?string $heading = 'Pending Holidays';

    protected static ?int $sort = 3;
    public ?string $filter = 'current_year';

    public static function canView(): bool
    {
        $currentYear = Carbon::now()->year;

        return Holiday::where('status', 'pending')
            ->where(function ($query) use ($currentYear) {
                $query->whereYear('start_date', $currentYear)
                    ->orWhereYear('end_date', $currentYear);
            })
            ->exists();
    }

    protected function getData(): array
    {
        $year = match ($this->filter) {
            'previous_year' => Carbon::now()->subYear()->year,
            'current_year' => Carbon::now()->year,
            'next_year' => Carbon::now()->addYear()->year,
            default => Carbon::now()->year,
        };

        $months = [];
        $holidayCounts = [];

        for ($i = 1; $i <= 12; $i++) {
            $month = Carbon::create($year, $i, 1);
            $months[] = $month->format('M Y');

            $count = Holiday::where('status', 'pending')
                ->where(function ($query) use ($month) {
                    $startOfMonth = $month->copy()->startOfMonth();
                    $endOfMonth = $month->copy()->endOfMonth();

                    $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                        ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                        ->orWhere(function ($subQuery) use ($startOfMonth, $endOfMonth) {
                            $subQuery->where('start_date', '<=', $startOfMonth)
                                ->where('end_date', '>=', $endOfMonth);
                        });
                })
                ->count();

            $holidayCounts[] = $count;
        }

        return [
            'datasets' => [
                [
                    'data' => $holidayCounts,
                    'borderWidth' => 1
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'previous_year' => 'Previous Year (' . Carbon::now()->subYear()->year . ')',
            'current_year' => 'Current Year (' . Carbon::now()->year . ')',
            'next_year' => 'Next Year (' . Carbon::now()->addYear()->year . ')',
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
