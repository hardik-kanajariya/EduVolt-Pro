<?php

namespace App\Filament\Admin\Pages;

use App\Models\School;
use App\Models\FeePayment;
use App\Models\FeeInstallment;
use App\Models\StudentFeeAssignment;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\Support\Htmlable;
use Carbon\Carbon;

class FinancialReports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Fee Management';
    protected static ?int $navigationSort = 6;
    protected static string $view = 'filament.admin.pages.financial-reports';

    public ?array $data = [];
    public array $reportData = [];
    public string $selectedReport = 'collection_summary';

    public function getTitle(): string|Htmlable
    {
        return 'Financial Reports';
    }

    public function mount(): void
    {
        $this->form->fill([
            'school_id' => auth()->user()?->school_id,
            'date_from' => now()->startOfMonth(),
            'date_to' => now()->endOfMonth(),
            'academic_year' => now()->year,
        ]);

        $this->generateReport();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('school_id')
                            ->label('School')
                            ->options(School::pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn() => $this->generateReport()),

                        Forms\Components\Select::make('report_type')
                            ->label('Report Type')
                            ->options([
                                'collection_summary' => 'Collection Summary',
                                'defaulter_list' => 'Defaulter List',
                                'payment_trends' => 'Payment Trends',
                                'fee_category_analysis' => 'Fee Category Analysis',
                                'outstanding_analysis' => 'Outstanding Analysis',
                                'monthly_comparison' => 'Monthly Comparison',
                            ])
                            ->default('collection_summary')
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->selectedReport = $state;
                                $this->generateReport();
                            }),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('From Date')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn() => $this->generateReport()),

                        Forms\Components\DatePicker::make('date_to')
                            ->label('To Date')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn() => $this->generateReport()),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('academic_year')
                            ->label('Academic Year')
                            ->numeric()
                            ->default(now()->year)
                            ->reactive()
                            ->afterStateUpdated(fn() => $this->generateReport()),

                        Forms\Components\Select::make('class_filter')
                            ->label('Class Filter (Optional)')
                            ->options([
                                'all' => 'All Classes',
                                'nursery' => 'Nursery',
                                'kg1' => 'KG1',
                                'kg2' => 'KG2',
                                '1' => 'Class 1',
                                '2' => 'Class 2',
                                '3' => 'Class 3',
                                '4' => 'Class 4',
                                '5' => 'Class 5',
                                '6' => 'Class 6',
                                '7' => 'Class 7',
                                '8' => 'Class 8',
                                '9' => 'Class 9',
                                '10' => 'Class 10',
                                '11' => 'Class 11',
                                '12' => 'Class 12',
                            ])
                            ->default('all')
                            ->reactive()
                            ->afterStateUpdated(fn() => $this->generateReport()),
                    ]),
            ])
            ->statePath('data');
    }

    public function generateReport(): void
    {
        $schoolId = $this->data['school_id'] ?? null;
        $dateFrom = $this->data['date_from'] ?? now()->startOfMonth();
        $dateTo = $this->data['date_to'] ?? now()->endOfMonth();
        $academicYear = $this->data['academic_year'] ?? now()->year;
        $classFilter = $this->data['class_filter'] ?? 'all';

        if (!$schoolId) {
            $this->reportData = [];
            return;
        }

        switch ($this->selectedReport) {
            case 'collection_summary':
                $this->reportData = $this->generateCollectionSummary($schoolId, $dateFrom, $dateTo);
                break;

            case 'defaulter_list':
                $this->reportData = $this->generateDefaulterList($schoolId, $classFilter);
                break;

            case 'payment_trends':
                $this->reportData = $this->generatePaymentTrends($schoolId, $academicYear);
                break;

            case 'fee_category_analysis':
                $this->reportData = $this->generateFeeCategoryAnalysis($schoolId, $dateFrom, $dateTo);
                break;

            case 'outstanding_analysis':
                $this->reportData = $this->generateOutstandingAnalysis($schoolId, $classFilter);
                break;

            case 'monthly_comparison':
                $this->reportData = $this->generateMonthlyComparison($schoolId, $academicYear);
                break;
        }
    }

    protected function generateCollectionSummary($schoolId, $dateFrom, $dateTo): array
    {
        $payments = FeePayment::whereHas('feeInstallment.studentFeeAssignment', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->with(['feeInstallment.studentFeeAssignment.feeStructure.feeCategory'])
            ->get();

        $totalCollection = $payments->sum('amount');
        $totalPayments = $payments->count();

        $paymentMethods = $payments->groupBy('payment_method')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('amount'),
                ];
            });

        $dailyCollection = $payments->groupBy(function ($payment) {
            return $payment->payment_date->format('Y-m-d');
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'amount' => $group->sum('amount'),
            ];
        });

        return [
            'total_collection' => $totalCollection,
            'total_payments' => $totalPayments,
            'average_payment' => $totalPayments > 0 ? $totalCollection / $totalPayments : 0,
            'payment_methods' => $paymentMethods,
            'daily_collection' => $dailyCollection,
            'period' => [
                'from' => Carbon::parse($dateFrom)->format('M d, Y'),
                'to' => Carbon::parse($dateTo)->format('M d, Y'),
            ],
        ];
    }

    protected function generateDefaulterList($schoolId, $classFilter): array
    {
        $query = FeeInstallment::whereHas('studentFeeAssignment', function ($q) use ($schoolId, $classFilter) {
            $q->where('school_id', $schoolId);
            if ($classFilter !== 'all') {
                $q->whereHas('student', function ($sq) use ($classFilter) {
                    $sq->where('class_name', $classFilter);
                });
            }
        })
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->with([
                'studentFeeAssignment.student',
                'studentFeeAssignment.feeStructure.feeCategory'
            ]);

        $defaulters = $query->get()->groupBy('studentFeeAssignment.student.id')
            ->map(function ($installments, $studentId) {
                $student = $installments->first()->studentFeeAssignment->student;
                $totalOverdue = $installments->sum('balance_amount');
                $overdueCount = $installments->count();
                $oldestDue = $installments->min('due_date');

                return [
                    'student_id' => $studentId,
                    'student_name' => $student->name,
                    'class' => $student->class_name,
                    'total_overdue' => $totalOverdue,
                    'overdue_count' => $overdueCount,
                    'oldest_due_date' => $oldestDue,
                    'days_overdue' => Carbon::parse($oldestDue)->diffInDays(now()),
                    'installments' => $installments->map(function ($installment) {
                        return [
                            'category' => $installment->studentFeeAssignment->feeStructure->feeCategory->name,
                            'installment' => $installment->installment_name,
                            'amount' => $installment->balance_amount,
                            'due_date' => $installment->due_date,
                        ];
                    }),
                ];
            })
            ->sortByDesc('total_overdue')
            ->values();

        return [
            'defaulters' => $defaulters,
            'summary' => [
                'total_defaulters' => $defaulters->count(),
                'total_overdue_amount' => $defaulters->sum('total_overdue'),
                'average_overdue' => $defaulters->count() > 0 ? $defaulters->sum('total_overdue') / $defaulters->count() : 0,
            ],
        ];
    }

    protected function generatePaymentTrends($schoolId, $academicYear): array
    {
        $startDate = Carbon::create($academicYear, 4, 1); // Academic year typically starts in April
        $endDate = $startDate->copy()->addYear()->subDay();

        $payments = FeePayment::whereHas('feeInstallment.studentFeeAssignment', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        $monthlyTrends = $payments->groupBy(function ($payment) {
            return $payment->payment_date->format('Y-m');
        })->map(function ($group, $month) {
            return [
                'month' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                'count' => $group->count(),
                'amount' => $group->sum('amount'),
                'average' => $group->count() > 0 ? $group->sum('amount') / $group->count() : 0,
            ];
        })->sortBy('month');

        return [
            'monthly_trends' => $monthlyTrends,
            'academic_year' => $academicYear,
            'total_amount' => $payments->sum('amount'),
            'total_payments' => $payments->count(),
        ];
    }

    protected function generateFeeCategoryAnalysis($schoolId, $dateFrom, $dateTo): array
    {
        $payments = FeePayment::whereHas('feeInstallment.studentFeeAssignment', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->with(['feeInstallment.studentFeeAssignment.feeStructure.feeCategory'])
            ->get();

        $categoryAnalysis = $payments->groupBy('feeInstallment.studentFeeAssignment.feeStructure.feeCategory.name')
            ->map(function ($group, $categoryName) {
                return [
                    'category' => $categoryName,
                    'count' => $group->count(),
                    'amount' => $group->sum('amount'),
                    'percentage' => 0, // Will be calculated below
                ];
            });

        $totalAmount = $categoryAnalysis->sum('amount');

        $categoryAnalysis = $categoryAnalysis->map(function ($category) use ($totalAmount) {
            $category['percentage'] = $totalAmount > 0 ? ($category['amount'] / $totalAmount) * 100 : 0;
            return $category;
        })->sortByDesc('amount');

        return [
            'categories' => $categoryAnalysis,
            'total_amount' => $totalAmount,
            'total_payments' => $payments->count(),
        ];
    }

    protected function generateOutstandingAnalysis($schoolId, $classFilter): array
    {
        $query = FeeInstallment::whereHas('studentFeeAssignment', function ($q) use ($schoolId, $classFilter) {
            $q->where('school_id', $schoolId);
            if ($classFilter !== 'all') {
                $q->whereHas('student', function ($sq) use ($classFilter) {
                    $sq->where('class_name', $classFilter);
                });
            }
        })
            ->where('status', '!=', 'paid')
            ->with([
                'studentFeeAssignment.student',
                'studentFeeAssignment.feeStructure.feeCategory'
            ]);

        $outstandingInstallments = $query->get();

        $classWiseOutstanding = $outstandingInstallments->groupBy('studentFeeAssignment.student.class_name')
            ->map(function ($group, $className) {
                return [
                    'class' => $className,
                    'students' => $group->groupBy('studentFeeAssignment.student.id')->count(),
                    'total_outstanding' => $group->sum('balance_amount'),
                    'installments' => $group->count(),
                ];
            })
            ->sortBy('class');

        $categoryWiseOutstanding = $outstandingInstallments->groupBy('studentFeeAssignment.feeStructure.feeCategory.name')
            ->map(function ($group, $categoryName) {
                return [
                    'category' => $categoryName,
                    'students' => $group->groupBy('studentFeeAssignment.student.id')->count(),
                    'total_outstanding' => $group->sum('balance_amount'),
                    'installments' => $group->count(),
                ];
            })
            ->sortByDesc('total_outstanding');

        return [
            'class_wise' => $classWiseOutstanding,
            'category_wise' => $categoryWiseOutstanding,
            'summary' => [
                'total_outstanding' => $outstandingInstallments->sum('balance_amount'),
                'total_students' => $outstandingInstallments->groupBy('studentFeeAssignment.student.id')->count(),
                'total_installments' => $outstandingInstallments->count(),
            ],
        ];
    }

    protected function generateMonthlyComparison($schoolId, $academicYear): array
    {
        $currentYear = Carbon::create($academicYear, 4, 1);
        $previousYear = $currentYear->copy()->subYear();

        $currentYearPayments = FeePayment::whereHas('feeInstallment.studentFeeAssignment', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
            ->whereBetween('payment_date', [$currentYear, $currentYear->copy()->addYear()->subDay()])
            ->get();

        $previousYearPayments = FeePayment::whereHas('feeInstallment.studentFeeAssignment', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
            ->whereBetween('payment_date', [$previousYear, $previousYear->copy()->addYear()->subDay()])
            ->get();

        $currentMonthly = $currentYearPayments->groupBy(function ($payment) {
            return $payment->payment_date->format('m');
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'amount' => $group->sum('amount'),
            ];
        });

        $previousMonthly = $previousYearPayments->groupBy(function ($payment) {
            return $payment->payment_date->format('m');
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'amount' => $group->sum('amount'),
            ];
        });

        $comparison = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
            $current = $currentMonthly->get($monthStr, ['count' => 0, 'amount' => 0]);
            $previous = $previousMonthly->get($monthStr, ['count' => 0, 'amount' => 0]);

            $comparison[] = [
                'month' => Carbon::create()->month($month)->format('M'),
                'current_amount' => $current['amount'],
                'previous_amount' => $previous['amount'],
                'current_count' => $current['count'],
                'previous_count' => $previous['count'],
                'amount_difference' => $current['amount'] - $previous['amount'],
                'count_difference' => $current['count'] - $previous['count'],
                'percentage_change' => $previous['amount'] > 0
                    ? (($current['amount'] - $previous['amount']) / $previous['amount']) * 100
                    : ($current['amount'] > 0 ? 100 : 0),
            ];
        }

        return [
            'comparison' => $comparison,
            'current_year' => $academicYear,
            'previous_year' => $academicYear - 1,
            'current_total' => $currentYearPayments->sum('amount'),
            'previous_total' => $previousYearPayments->sum('amount'),
        ];
    }
}
