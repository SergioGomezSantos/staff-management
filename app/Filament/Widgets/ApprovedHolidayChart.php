<?php

namespace App\Filament\Widgets;

use App\Models\Holiday;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ApprovedHolidayChart extends ChartWidget
{
    protected static ?string $heading = 'Approved Holidays';

    protected static ?int $sort = 2;
    public ?string $filter = 'current_year';

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

            $count = Holiday::where('status', 'approved')
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
                    'borderWidth' => 1,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)', // success-500 20% opacity
                    'borderColor' => 'rgb(21, 128, 61)' // success 600
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