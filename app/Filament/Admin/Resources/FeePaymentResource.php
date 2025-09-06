<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FeePaymentResource\Pages;
use App\Models\FeePayment;
use App\Models\Student;
use App\Models\School;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FeePaymentResource extends Resource
{
    protected static ?string $model = FeePayment::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Fee Management';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Fee Payments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('receipt_number')
                                    ->label('Receipt Number')
                                    ->disabled()
                                    ->hint('Auto-generated on save'),

                                Forms\Components\Select::make('student_id')
                                    ->label('Student')
                                    ->options(function () {
                                        return Student::with('user', 'schoolClass')
                                            ->get()
                                            ->mapWithKeys(function ($student) {
                                                $label = $student->user->name . ' (' . $student->admission_number . ') - ' . $student->schoolClass->name;
                                                return [$student->id => $label];
                                            });
                                    })
                                    ->required()
                                    ->reactive()
                                    ->searchable(),

                                Forms\Components\Select::make('school_id')
                                    ->label('School')
                                    ->options(School::active()->pluck('name', 'id'))
                                    ->required()
                                    ->reactive()
                                    ->searchable(),
                            ]),
                    ])
                    ->heading('Payment Details'),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->numeric()
                                    ->required()
                                    ->prefix('$')
                                    ->reactive(),

                                Forms\Components\TextInput::make('late_fee_amount')
                                    ->label('Late Fee')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$')
                                    ->reactive(),

                                Forms\Components\TextInput::make('discount_amount')
                                    ->label('Discount')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$')
                                    ->reactive(),

                                Forms\Components\TextInput::make('adjustment_amount')
                                    ->label('Adjustment')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$')
                                    ->hint('+/- adjustment amount')
                                    ->reactive(),
                            ]),

                        Forms\Components\TextInput::make('net_amount')
                            ->label('Net Amount')
                            ->numeric()
                            ->disabled()
                            ->prefix('$')
                            ->reactive()
                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, callable $get) {
                                $total = $get('total_amount') ?? 0;
                                $lateFee = $get('late_fee_amount') ?? 0;
                                $discount = $get('discount_amount') ?? 0;
                                $adjustment = $get('adjustment_amount') ?? 0;
                                $component->state($total + $lateFee - $discount + $adjustment);
                            }),
                    ])
                    ->heading('Amount Breakdown'),

                Forms\Components\Card::make()
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
                                        'online' => 'Online',
                                    ])
                                    ->default('cash')
                                    ->required()
                                    ->reactive(),

                                Forms\Components\TextInput::make('transaction_reference')
                                    ->label('Transaction Reference')
                                    ->placeholder('Cheque No., Transaction ID, etc.')
                                    ->visible(fn(callable $get) => in_array($get('payment_method'), ['cheque', 'bank_transfer', 'card', 'online'])),

                                Forms\Components\Select::make('collected_by')
                                    ->label('Collected By')
                                    ->options(User::whereHas('roles', function ($query) {
                                        $query->whereIn('name', ['admin', 'accountant', 'staff']);
                                    })->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->default(Auth::id()),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('payment_date')
                                    ->label('Payment Date')
                                    ->required()
                                    ->default(now()),

                                Forms\Components\DateTimePicker::make('payment_time')
                                    ->label('Payment Time')
                                    ->default(now()),
                            ]),
                    ])
                    ->heading('Payment Information'),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'completed' => 'Completed',
                                'pending' => 'Pending',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('completed')
                            ->required(),

                        Forms\Components\Textarea::make('remarks')
                            ->rows(3)
                            ->placeholder('Payment remarks or notes'),

                        Forms\Components\KeyValue::make('payment_breakdown')
                            ->label('Payment Breakdown')
                            ->keyLabel('Description')
                            ->valueLabel('Amount')
                            ->hint('Detailed breakdown of payment allocation'),
                    ])
                    ->heading('Additional Information'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->label('Receipt No.')
                    ->sortable()
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Student Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.admission_number')
                    ->label('Admission No.')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.schoolClass.name')
                    ->label('Class')
                    ->sortable(),

                Tables\Columns\TextColumn::make('formatted_net_amount')
                    ->label('Amount Paid')
                    ->sortable('net_amount'),

                Tables\Columns\TextColumn::make('payment_method_label')
                    ->label('Payment Method')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'cash' => 'success',
                        'cheque' => 'info',
                        'bank_transfer' => 'warning',
                        'card' => 'primary',
                        'online' => 'secondary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Payment Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('collectedBy.name')
                    ->label('Collected By')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        'cancelled' => 'secondary',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_printed')
                    ->boolean()
                    ->label('Printed'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school_id')
                    ->label('School')
                    ->options(School::active()->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'cheque' => 'Cheque',
                        'bank_transfer' => 'Bank Transfer',
                        'card' => 'Card',
                        'online' => 'Online',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('collected_by')
                    ->label('Collected By')
                    ->options(User::pluck('name', 'id')),

                Tables\Filters\Filter::make('payment_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
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

                Tables\Filters\TernaryFilter::make('is_printed')
                    ->label('Receipt Printed'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => $record->status === 'pending'),
                Tables\Actions\Action::make('printReceipt')
                    ->label('Print Receipt')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->action(function ($record) {
                        $record->markAsPrinted();
                        // Generate and download PDF receipt
                        return response()->streamDownload(function () use ($record) {
                            echo static::generateReceiptHTML($record);
                        }, "receipt_{$record->receipt_number}.html", [
                            'Content-Type' => 'text/html',
                        ]);
                    }),
                Tables\Actions\Action::make('refund')
                    ->label('Refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->visible(fn($record) => $record->canBeRefunded())
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'refunded']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('markAsPrinted')
                        ->label('Mark as Printed')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->action(function ($records) {
                            $records->each->markAsPrinted();
                        }),
                ]),
            ])
            ->defaultSort('payment_date', 'desc');
    }

    public static function generateReceiptHTML($payment): string
    {
        $student = $payment->feeInstallment->studentFeeAssignment->student;
        $feeCategory = $payment->feeInstallment->studentFeeAssignment->feeStructure->feeCategory;
        $installment = $payment->feeInstallment;

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Fee Payment Receipt</title>
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
                <div class='info-row'>
                    <span class='label'>Payment Status:</span>
                    <span class='value'>" . ucfirst($payment->status) . "</span>
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeePayments::route('/'),
            'create' => Pages\CreateFeePayment::route('/create'),
            'view' => Pages\ViewFeePayment::route('/{record}'),
            'edit' => Pages\EditFeePayment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'student.user',
            'student.schoolClass',
            'school',
            'collectedBy'
        ]);
    }
}
