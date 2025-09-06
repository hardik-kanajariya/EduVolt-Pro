<?php

namespace App\Filament\Parent\Pages;

use App\Models\Student;
use App\Models\FeePayment;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class PaymentHistory extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.parent.pages.payment-history';

    public ?Student $selectedChild = null;
    public array $paymentSummary = [];

    public function getTitle(): string|Htmlable
    {
        return 'Payment History';
    }

    public function mount(): void
    {
        // Get first child by default
        $this->selectedChild = $this->getChildren()->first();
        $this->loadPaymentSummary();
    }

    public function getChildren()
    {
        return Student::whereHas('user', function ($query) {
            $query->where('email', auth()->user()?->email);
        })->get();
    }

    public function selectChild($childId): void
    {
        $this->selectedChild = Student::find($childId);
        $this->loadPaymentSummary();
        $this->resetTable();
    }

    protected function loadPaymentSummary(): void
    {
        if (!$this->selectedChild) {
            $this->paymentSummary = [];
            return;
        }

        $payments = FeePayment::whereHas('feeInstallment.studentFeeAssignment', function ($query) {
            $query->where('student_id', $this->selectedChild->id);
        })->get();

        $this->paymentSummary = [
            'total_payments' => $payments->count(),
            'total_amount_paid' => $payments->sum('amount'),
            'last_payment_date' => $payments->max('payment_date'),
            'last_payment_amount' => $payments->where('payment_date', $payments->max('payment_date'))->first()?->amount ?? 0,
            'payments_this_month' => $payments->where('payment_date', '>=', now()->startOfMonth())->count(),
            'amount_this_month' => $payments->where('payment_date', '>=', now()->startOfMonth())->sum('amount'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                FeePayment::query()
                    ->when($this->selectedChild, function ($query) {
                        return $query->whereHas('feeInstallment.studentFeeAssignment', function ($q) {
                            $q->where('student_id', $this->selectedChild->id);
                        });
                    })
                    ->with([
                        'feeInstallment.studentFeeAssignment.feeStructure.feeCategory',
                        'user'
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->label('Receipt #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Payment Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('feeInstallment.studentFeeAssignment.feeStructure.feeCategory.name')
                    ->label('Fee Category')
                    ->sortable(),

                Tables\Columns\TextColumn::make('feeInstallment.installment_name')
                    ->label('Installment')
                    ->sortable(),

                Tables\Columns\TextColumn::make('formatted_amount')
                    ->label('Amount Paid')
                    ->sortable('amount'),

                Tables\Columns\BadgeColumn::make('payment_method')
                    ->colors([
                        'success' => 'cash',
                        'info' => 'bank_transfer',
                        'warning' => 'cheque',
                        'primary' => 'online',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'pending',
                        'danger' => 'failed',
                        'info' => 'processing',
                    ]),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Collected By')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Reference')
                    ->searchable()
                    ->placeholder('N/A'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'bank_transfer' => 'Bank Transfer',
                        'cheque' => 'Cheque',
                        'online' => 'Online',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                        'processing' => 'Processing',
                    ]),

                Tables\Filters\Filter::make('payment_date')
                    ->form([
                        Tables\Filters\Indicator::make('from')
                            ->label('From Date')
                            ->date(),
                        Tables\Filters\Indicator::make('until')
                            ->label('Until Date')
                            ->date(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadReceipt')
                    ->label('Receipt')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->visible(fn($record) => $record->status === 'completed')
                    ->action(function ($record) {
                        // Generate and download receipt PDF
                        return response()->streamDownload(function () use ($record) {
                            echo $this->generateReceiptContent($record);
                        }, "receipt_{$record->receipt_number}.html", [
                            'Content-Type' => 'text/html',
                        ]);
                    }),

                Tables\Actions\Action::make('printReceipt')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('secondary')
                    ->visible(fn($record) => $record->status === 'completed')
                    ->action(function ($record) {
                        // Open print dialog
                        $this->dispatch('print-receipt', receiptId: $record->id);
                    }),
            ])
            ->defaultSort('payment_date', 'desc');
    }

    protected function generateReceiptContent(FeePayment $payment): string
    {
        $installment = $payment->feeInstallment;
        $student = $installment->studentFeeAssignment->student;
        $feeCategory = $installment->studentFeeAssignment->feeStructure->feeCategory;

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payment Receipt</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                .content { margin: 20px 0; }
                .row { display: flex; justify-content: space-between; margin: 10px 0; }
                .label { font-weight: bold; }
                .amount { font-size: 18px; font-weight: bold; color: #2563eb; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h2>Payment Receipt</h2>
                <p>Receipt #: {$payment->receipt_number}</p>
            </div>
            
            <div class='content'>
                <div class='row'><span class='label'>Student Name:</span> <span>{$student->name}</span></div>
                <div class='row'><span class='label'>Class:</span> <span>{$student->class_name}</span></div>
                <div class='row'><span class='label'>Fee Category:</span> <span>{$feeCategory->name}</span></div>
                <div class='row'><span class='label'>Installment:</span> <span>{$installment->installment_name}</span></div>
                <div class='row'><span class='label'>Payment Date:</span> <span>{$payment->payment_date->format('M d, Y')}</span></div>
                <div class='row'><span class='label'>Payment Method:</span> <span>{$payment->payment_method}</span></div>
                <div class='row'><span class='label'>Amount Paid:</span> <span class='amount'>₹{$payment->formatted_amount}</span></div>
                <div class='row'><span class='label'>Collected By:</span> <span>{$payment->user->name}</span></div>
            </div>
            
            <div style='margin-top: 40px; text-align: center; font-size: 12px; color: #666;'>
                <p>This is a computer generated receipt.</p>
                <p>Generated on: " . now()->format('M d, Y H:i:s') . "</p>
            </div>
        </body>
        </html>";
    }

    public function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
    }
}
