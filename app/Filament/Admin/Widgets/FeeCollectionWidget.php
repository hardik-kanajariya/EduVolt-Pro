<?php

namespace App\Filament\Admin\Widgets;

use App\Models\FeePayment;
use App\Models\FeeInstallment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FeeCollectionWidget extends ChartWidget
{
    protected static ?string $heading = 'Monthly Fee Collection';

    protected function getData(): array
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        $monthlyData = [];
        $labels = [];

        // Get data for last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $collected = FeePayment::whereHas('installments.studentFeeAssignment.student', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->where('status', 'completed')
                ->sum('amount_paid');

            $monthlyData[] = $collected;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Fee Collection (₹)',
                    'data' => $monthlyData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 3,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
