<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SchoolFinance extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'month_year',
        'revenue',
        'expenses',
        'profit_loss',
        'fee_collection',
        'salary_expenses',
        'operational_expenses',
        'breakdown',
        'notes',
    ];

    protected $casts = [
        'revenue' => 'float',
        'expenses' => 'float',
        'profit_loss' => 'float',
        'fee_collection' => 'float',
        'salary_expenses' => 'float',
        'operational_expenses' => 'float',
        'breakdown' => 'array',
    ];

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get or create current month record for a school
     */
    public static function getOrCreateCurrentMonth(int $schoolId): static
    {
        $monthYear = Carbon::now()->format('Y-m');

        return static::firstOrCreate(
            [
                'school_id' => $schoolId,
                'month_year' => $monthYear,
            ],
            [
                'revenue' => 0,
                'expenses' => 0,
                'profit_loss' => 0,
                'fee_collection' => 0,
                'salary_expenses' => 0,
                'operational_expenses' => 0,
            ]
        );
    }

    /**
     * Calculate and update profit/loss
     */
    public function calculateProfitLoss(): self
    {
        $this->profit_loss = (float)$this->revenue - (float)$this->expenses;
        $this->save();

        return $this;
    }

    /**
     * Add revenue
     */
    public function addRevenue(float $amount, string $source = 'general'): self
    {
        $this->revenue = (float)$this->revenue + $amount;

        $breakdown = $this->breakdown ?? [];
        $breakdown['revenue'][$source] = ($breakdown['revenue'][$source] ?? 0) + $amount;
        $this->breakdown = $breakdown;

        $this->calculateProfitLoss();

        return $this;
    }

    /**
     * Add expense
     */
    public function addExpense(float $amount, string $category = 'general'): self
    {
        $this->expenses = (float)$this->expenses + $amount;

        $breakdown = $this->breakdown ?? [];
        $breakdown['expenses'][$category] = ($breakdown['expenses'][$category] ?? 0) + $amount;
        $this->breakdown = $breakdown;

        $this->calculateProfitLoss();

        return $this;
    }

    /**
     * Get financial summary for a school across months
     */
    public static function getSchoolSummary(int $schoolId, int $months = 12): array
    {
        $finances = static::where('school_id', $schoolId)
            ->orderBy('month_year', 'desc')
            ->limit($months)
            ->get();

        return [
            'total_revenue' => $finances->sum('revenue'),
            'total_expenses' => $finances->sum('expenses'),
            'total_profit_loss' => $finances->sum('profit_loss'),
            'average_monthly_revenue' => $finances->avg('revenue'),
            'average_monthly_expenses' => $finances->avg('expenses'),
            'months_data' => $finances->map(function ($finance) {
                return [
                    'month_year' => $finance->month_year,
                    'revenue' => $finance->revenue,
                    'expenses' => $finance->expenses,
                    'profit_loss' => $finance->profit_loss,
                ];
            })->toArray(),
        ];
    }

    /**
     * Get global financial overview for super admin
     */
    public static function getGlobalSummary(int $months = 12): array
    {
        $currentMonth = Carbon::now()->subMonths($months)->format('Y-m');

        $finances = static::where('month_year', '>=', $currentMonth)
            ->with('school:id,name')
            ->get();

        $schoolSummaries = $finances->groupBy('school_id')->map(function ($schoolFinances) {
            $school = $schoolFinances->first()->school;
            return [
                'school_name' => $school->name,
                'total_revenue' => $schoolFinances->sum('revenue'),
                'total_expenses' => $schoolFinances->sum('expenses'),
                'total_profit_loss' => $schoolFinances->sum('profit_loss'),
            ];
        });

        return [
            'overall_total_revenue' => $finances->sum('revenue'),
            'overall_total_expenses' => $finances->sum('expenses'),
            'overall_total_profit_loss' => $finances->sum('profit_loss'),
            'school_summaries' => $schoolSummaries->values()->toArray(),
            'months_analyzed' => $months,
        ];
    }

    // Scopes
    public function scopeByMonthYear($query, string $monthYear)
    {
        return $query->where('month_year', $monthYear);
    }

    public function scopeCurrentMonth($query)
    {
        return $query->where('month_year', Carbon::now()->format('Y-m'));
    }

    public function scopeLastMonths($query, int $months = 12)
    {
        $startMonth = Carbon::now()->subMonths($months)->format('Y-m');
        return $query->where('month_year', '>=', $startMonth);
    }
}
