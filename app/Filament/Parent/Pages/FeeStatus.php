<?php

namespace App\Filament\Parent\Pages;

use App\Models\Student;
use App\Models\FeePayment;
use App\Models\FeeInstallment;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class FeeStatus extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.parent.pages.fee-status';

    public ?Student $selectedChild = null;
    public array $feeStatistics = [];

    public function getTitle(): string|Htmlable
    {
        return 'Fee Status';
    }

    public function mount(): void
    {
        // Get first child by default
        $this->selectedChild = $this->getChildren()->first();
        $this->loadFeeStatistics();
    }

    public function getChildren()
    {
        // Assuming parent user relationship exists
        return Student::whereHas('user', function ($query) {
            $query->where('email', Auth::user()?->email); // or however parent-child relationship is defined
        })->get();
    }

    public function selectChild($childId): void
    {
        $this->selectedChild = Student::find($childId);
        $this->loadFeeStatistics();
        $this->resetTable();
    }

    protected function loadFeeStatistics(): void
    {
        if (!$this->selectedChild) {
            $this->feeStatistics = [];
            return;
        }

        $installments = FeeInstallment::whereHas('studentFeeAssignment', function ($query) {
            $query->where('student_id', $this->selectedChild->id);
        })->get();

        $this->feeStatistics = [
            'total_fee' => $installments->sum('amount'),
            'paid_amount' => $installments->sum('paid_amount'),
            'outstanding' => $installments->sum('balance_amount'),
            'overdue_count' => $installments->where('is_overdue', true)->count(),
            'overdue_amount' => $installments->where('is_overdue', true)->sum('balance_amount'),
            'next_due_date' => $installments->where('status', 'pending')->min('due_date'),
            'next_due_amount' => $installments->where('status', 'pending')->first()?->balance_amount ?? 0,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                FeeInstallment::query()
                    ->when($this->selectedChild, function ($query) {
                        return $query->whereHas('studentFeeAssignment', function ($q) {
                            $q->where('student_id', $this->selectedChild->id);
                        });
                    })
                    ->with(['studentFeeAssignment.feeStructure.feeCategory'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('studentFeeAssignment.feeStructure.feeCategory.name')
                    ->label('Fee Category')
                    ->sortable(),

                Tables\Columns\TextColumn::make('installment_name')
                    ->label('Installment')
                    ->sortable(),

                Tables\Columns\TextColumn::make('formatted_amount')
                    ->label('Amount')
                    ->sortable('amount'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'partially_paid' => 'warning',
                        'overdue' => 'danger',
                        'pending' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('formatted_balance_amount')
                    ->label('Outstanding')
                    ->sortable('balance_amount'),

                Tables\Columns\TextColumn::make('last_payment_date')
                    ->label('Last Payment')
                    ->date()
                    ->sortable()
                    ->placeholder('No payment'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'partially_paid' => 'Partially Paid',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                    ]),

                Tables\Filters\SelectFilter::make('studentFeeAssignment.feeStructure.feeCategory.name')
                    ->label('Fee Category')
                    ->options(function () {
                        if (!$this->selectedChild) {
                            return [];
                        }

                        return FeeInstallment::whereHas('studentFeeAssignment', function ($query) {
                            $query->where('student_id', $this->selectedChild->id);
                        })
                            ->with('studentFeeAssignment.feeStructure.feeCategory')
                            ->get()
                            ->pluck('studentFeeAssignment.feeStructure.feeCategory.name', 'studentFeeAssignment.feeStructure.feeCategory.name')
                            ->unique();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('payOnline')
                    ->label('Pay Online')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->visible(fn($record) => $record->status !== 'paid')
                    ->action(function () {
                        // Show message for v1.0
                        \Filament\Notifications\Notification::make()
                            ->title('Online Payment Not Available')
                            ->body('Please visit the school office for fee payment. Online payment will be available in the next version.')
                            ->info()
                            ->send();
                    }),
            ])
            ->defaultSort('due_date', 'asc');
    }

    public function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
    }
}
