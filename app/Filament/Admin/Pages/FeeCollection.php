<?php

namespace App\Filament\Admin\Pages;

use App\Models\Student;
use App\Models\FeeInstallment;
use App\Models\FeePayment;
use App\Models\School;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class FeeCollection extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Fee Management';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.admin.pages.fee-collection';

    public ?array $data = [];
    public ?Student $selectedStudent = null;
    public ?array $pendingInstallments = [];
    public ?array $selectedInstallments = [];
    public float $totalAmount = 0;
    public float $lateFeeAmount = 0;
    public float $discountAmount = 0;
    public float $netAmount = 0;

    public function getTitle(): string|Htmlable
    {
        return 'Fee Collection';
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Student Search')
                    ->schema([
                        Forms\Components\TextInput::make('search_student')
                            ->label('Search Student')
                            ->placeholder('Enter admission number or student name')
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($state) {
                                $this->searchStudent($state);
                            }),
                    ]),

                Forms\Components\Section::make('Student Information')
                    ->schema([
                        Forms\Components\Placeholder::make('student_info')
                            ->label('')
                            ->content(function () {
                                if (!$this->selectedStudent) {
                                    return 'No student selected';
                                }

                                return view('filament.admin.components.student-info', [
                                    'student' => $this->selectedStudent
                                ]);
                            }),
                    ])
                    ->visible(fn() => $this->selectedStudent !== null),

                Forms\Components\Section::make('Pending Installments')
                    ->schema([
                        Forms\Components\CheckboxList::make('selected_installments')
                            ->label('Select Installments to Pay')
                            ->options(function () {
                                return collect($this->pendingInstallments)->mapWithKeys(function ($installment) {
                                    $label = $installment['name'] . ' - ' . $installment['formatted_amount'] .
                                        ($installment['is_overdue'] ? ' (Overdue)' : '');
                                    return [$installment['id'] => $label];
                                });
                            })
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->calculateAmounts($state);
                            }),
                    ])
                    ->visible(fn() => !empty($this->pendingInstallments)),

                Forms\Components\Section::make('Payment Summary')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->prefix('$')
                                    ->disabled(),

                                Forms\Components\TextInput::make('late_fee_amount')
                                    ->label('Late Fee')
                                    ->prefix('$')
                                    ->disabled(),

                                Forms\Components\TextInput::make('discount_amount')
                                    ->label('Discount')
                                    ->prefix('$')
                                    ->numeric()
                                    ->live()
                                    ->afterStateUpdated(function () {
                                        $this->calculateNetAmount();
                                    }),

                                Forms\Components\TextInput::make('net_amount')
                                    ->label('Net Amount')
                                    ->prefix('$')
                                    ->disabled(),
                            ]),
                    ]),

                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('payment_method')
                                    ->label('Payment Method')
                                    ->options([
                                        'cash' => 'Cash',
                                        'cheque' => 'Cheque',
                                        'bank_transfer' => 'Bank Transfer',
                                        'card' => 'Card',
                                    ])
                                    ->default('cash')
                                    ->required(),

                                Forms\Components\TextInput::make('transaction_reference')
                                    ->label('Transaction Reference')
                                    ->placeholder('Cheque No., Transaction ID, etc.'),

                                Forms\Components\DatePicker::make('payment_date')
                                    ->label('Payment Date')
                                    ->default(now())
                                    ->required(),
                            ]),

                        Forms\Components\Textarea::make('remarks')
                            ->label('Remarks')
                            ->rows(2),
                    ])
                    ->heading('Payment Details')
                    ->visible(fn() => !empty($this->selectedInstallments)),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('collectPayment')
                ->label('Collect Payment')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->visible(fn() => !empty($this->selectedInstallments))
                ->action('collectPayment'),

            Action::make('printReceipt')
                ->label('Print Receipt')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->visible(fn() => !empty($this->selectedInstallments))
                ->action('printReceipt'),

            Action::make('clear')
                ->label('Clear')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->action('clearForm'),
        ];
    }

    public function searchStudent($search): void
    {
        if (empty($search)) {
            $this->selectedStudent = null;
            $this->pendingInstallments = [];
            return;
        }

        $student = Student::with(['user', 'schoolClass', 'feeAssignments.feeInstallments'])
            ->where(function ($query) use ($search) {
                $query->where('admission_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->first();

        if ($student) {
            $this->selectedStudent = $student;
            $this->loadPendingInstallments();
        } else {
            $this->selectedStudent = null;
            $this->pendingInstallments = [];
        }
    }

    protected function loadPendingInstallments(): void
    {
        if (!$this->selectedStudent) {
            return;
        }

        $installments = FeeInstallment::whereHas('studentFeeAssignment', function ($query) {
            $query->where('student_id', $this->selectedStudent->id)
                ->where('is_active', true);
        })
            ->where('status', '!=', 'paid')
            ->orderBy('due_date')
            ->get();

        $this->pendingInstallments = $installments->map(function ($installment) {
            return [
                'id' => $installment->id,
                'name' => $installment->installment_name,
                'amount' => $installment->balance_amount,
                'formatted_amount' => $installment->formatted_balance_amount,
                'due_date' => $installment->due_date->format('d/m/Y'),
                'is_overdue' => $installment->is_overdue,
                'late_fee' => $installment->late_fee_amount,
            ];
        })->toArray();
    }

    public function calculateAmounts($selectedIds): void
    {
        $this->selectedInstallments = $selectedIds ?? [];

        if (empty($this->selectedInstallments)) {
            $this->totalAmount = 0;
            $this->lateFeeAmount = 0;
            $this->netAmount = 0;
            return;
        }

        $selectedInstallments = collect($this->pendingInstallments)
            ->whereIn('id', $this->selectedInstallments);

        $this->totalAmount = $selectedInstallments->sum('amount');
        $this->lateFeeAmount = $selectedInstallments->sum('late_fee');

        $this->calculateNetAmount();
    }

    protected function calculateNetAmount(): void
    {
        $this->netAmount = $this->totalAmount + $this->lateFeeAmount - ($this->data['discount_amount'] ?? 0);
    }

    public function collectPayment(): void
    {
        if (empty($this->selectedInstallments)) {
            Notification::make()
                ->title('No installments selected')
                ->danger()
                ->send();
            return;
        }

        try {
            $payment = FeePayment::create([
                'student_id' => $this->selectedStudent->id,
                'school_id' => $this->selectedStudent->school_id,
                'academic_year_id' => $this->selectedStudent->schoolClass->academic_year_id,
                'installment_ids' => $this->selectedInstallments,
                'total_amount' => $this->totalAmount,
                'late_fee_amount' => $this->lateFeeAmount,
                'discount_amount' => $this->data['discount_amount'] ?? 0,
                'adjustment_amount' => 0,
                'net_amount' => $this->netAmount,
                'payment_method' => $this->data['payment_method'],
                'transaction_reference' => $this->data['transaction_reference'],
                'payment_date' => $this->data['payment_date'],
                'payment_time' => now(),
                'collected_by' => Auth::id(),
                'status' => 'completed',
                'remarks' => $this->data['remarks'],
            ]);

            Notification::make()
                ->title('Payment collected successfully')
                ->success()
                ->body("Receipt Number: {$payment->receipt_number}")
                ->send();

            $this->clearForm();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Payment collection failed')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    public function downloadReceipt($paymentId)
    {
        // Generate and download receipt
        return response()->streamDownload(function () use ($paymentId) {
            echo $this->generateReceiptHTML($paymentId);
        }, "receipt_{$paymentId}.html", [
            'Content-Type' => 'text/html',
        ]);
    }

    protected function generateReceiptHTML($paymentId): string
    {
        $payment = FeePayment::with([
            'feeInstallment.studentFeeAssignment.student',
            'feeInstallment.studentFeeAssignment.feeStructure.feeCategory',
            'user'
        ])->findOrFail($paymentId);

        $student = $payment->feeInstallment->studentFeeAssignment->student;
        $feeCategory = $payment->feeInstallment->studentFeeAssignment->feeStructure->feeCategory;
        $installment = $payment->feeInstallment;

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Fee Receipt</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
                .school-name { font-size: 24px; font-weight: bold; color: #2563eb; margin-bottom: 5px; }
                .receipt-title { font-size: 18px; margin: 10px 0; }
                .receipt-no { font-weight: bold; color: #dc2626; }
                .content { margin: 20px 0; }
                .info-row { display: flex; justify-content: space-between; margin: 8px 0; padding: 5px 0; border-bottom: 1px dotted #ccc; }
                .label { font-weight: bold; color: #374151; }
                .value { color: #1f2937; }
                .amount-section { background: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0; }
                .total-amount { font-size: 20px; font-weight: bold; color: #059669; text-align: center; }
                .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #6b7280; }
                .signature-section { margin-top: 30px; display: flex; justify-content: space-between; }
                .signature-box { text-align: center; width: 200px; }
                .signature-line { border-top: 1px solid #333; margin-top: 40px; padding-top: 5px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <div class='school-name'>{$student->school->name}</div>
                <div>{$student->school->address}</div>
                <div>Phone: {$student->school->phone} | Email: {$student->school->email}</div>
                <div class='receipt-title'>FEE PAYMENT RECEIPT</div>
                <div class='receipt-no'>Receipt No: {$payment->receipt_number}</div>
            </div>
            
            <div class='content'>
                <div class='info-row'>
                    <span class='label'>Student Name:</span>
                    <span class='value'>{$student->name}</span>
                </div>
                <div class='info-row'>
                    <span class='label'>Admission Number:</span>
                    <span class='value'>{$student->admission_number}</span>
                </div>
                <div class='info-row'>
                    <span class='label'>Class:</span>
                    <span class='value'>{$student->class_name}</span>
                </div>
                <div class='info-row'>
                    <span class='label'>Fee Category:</span>
                    <span class='value'>{$feeCategory->name}</span>
                </div>
                <div class='info-row'>
                    <span class='label'>Installment:</span>
                    <span class='value'>{$installment->installment_name}</span>
                </div>
                <div class='info-row'>
                    <span class='label'>Payment Date:</span>
                    <span class='value'>" . $payment->payment_date->format('d M Y') . "</span>
                </div>
                <div class='info-row'>
                    <span class='label'>Payment Method:</span>
                    <span class='value'>" . ucfirst(str_replace('_', ' ', $payment->payment_method)) . "</span>
                </div>
                
                <div class='amount-section'>
                    <div class='info-row'>
                        <span class='label'>Fee Amount:</span>
                        <span class='value'>{$payment->formatted_amount}</span>
                    </div>
                    <div class='info-row'>
                        <span class='label'>Late Fee:</span>
                        <span class='value'>₹" . number_format($payment->late_fee ?? 0, 2) . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='label'>Discount:</span>
                        <span class='value'>₹" . number_format($payment->discount_amount ?? 0, 2) . "</span>
                    </div>
                    <div class='total-amount'>
                        Total Amount Paid: {$payment->formatted_total_amount}
                    </div>
                </div>
                
                <div class='info-row'>
                    <span class='label'>Collected By:</span>
                    <span class='value'>{$payment->user->name}</span>
                </div>
                <div class='info-row'>
                    <span class='label'>Reference Number:</span>
                    <span class='value'>" . ($payment->reference_number ?? 'N/A') . "</span>
                </div>
            </div>
            
            <div class='signature-section'>
                <div class='signature-box'>
                    <div class='signature-line'>Student/Parent Signature</div>
                </div>
                <div class='signature-box'>
                    <div class='signature-line'>Cashier Signature</div>
                </div>
            </div>
            
            <div class='footer'>
                <p><strong>Note:</strong> This is a computer generated receipt. Please keep this receipt for your records.</p>
                <p>Receipt generated on: " . now()->format('d M Y H:i:s') . "</p>
                <p>Thank you for your payment!</p>
            </div>
        </body>
        </html>";
    }

    protected function printReceipt($paymentId): void
    {
        // Open print dialog in browser
        $this->dispatch('print-receipt', receiptId: $paymentId);

        Notification::make()
            ->title('Receipt ready for printing')
            ->success()
            ->send();
    }

    public function clearForm(): void
    {
        $this->selectedStudent = null;
        $this->pendingInstallments = [];
        $this->selectedInstallments = [];
        $this->totalAmount = 0;
        $this->lateFeeAmount = 0;
        $this->discountAmount = 0;
        $this->netAmount = 0;
        $this->form->fill();
    }
}
