<?php

namespace App\Filament\Admin\Widgets;

use App\Models\FeePayment;
use App\Models\Student;
use App\Models\FeeInstallment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FinancialStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        $monthlyCollection = FeePayment::whereHas('installments.studentFeeAssignment.student', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
            ->whereMonth('payment_date', now()->month)
            ->sum('amount_paid');

        $totalPending = FeeInstallment::whereHas('studentFeeAssignment.student', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->sum('amount');

        $totalStudentsWithDues = FeeInstallment::whereHas('studentFeeAssignment.student', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
            ->where('status', 'pending')
            ->distinct('student_fee_assignment_id')
            ->count();

        $todayCollection = FeePayment::whereHas('installments.studentFeeAssignment.student', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
            ->whereDate('payment_date', today())
            ->sum('amount_paid');

        return [
            Stat::make('Monthly Collection', '₹' . number_format($monthlyCollection))
                ->chart([30000, 45000, 52000, 48000, 65000, 70000, 75000])
                ->color('success'),

            Stat::make('Pending Dues', '₹' . number_format($totalPending))
                ->chart([150000, 140000, 130000, 125000, 120000, 115000, 110000])
                ->color('danger'),

            Stat::make('Students with Dues', $totalStudentsWithDues)
                ->chart([45, 42, 38, 35, 32, 28, 25])
                ->color('warning'),

            Stat::make('Today\'s Collection', '₹' . number_format($todayCollection))
                ->chart([2000, 3500, 4200, 3800, 5100, 4700, 5500])
                ->color('info'),
        ];
    }
}
