<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StudentFeeAssignmentResource\Pages;
use App\Models\StudentFeeAssignment;
use App\Models\Student;
use App\Models\FeeStructure;
use App\Models\School;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentFeeAssignmentResource extends Resource
{
    protected static ?string $model = StudentFeeAssignment::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Fee Management';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Student Fee Assignments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
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

                                Forms\Components\Select::make('fee_structure_id')
                                    ->label('Fee Structure')
                                    ->options(function (callable $get) {
                                        $studentId = $get('student_id');
                                        if (!$studentId) {
                                            return [];
                                        }

                                        $student = Student::find($studentId);
                                        if (!$student) {
                                            return [];
                                        }

                                        return FeeStructure::where('school_id', $student->school_id)
                                            ->where('class_id', $student->class_id)
                                            ->with(['feeCategory', 'academicYear'])
                                            ->get()
                                            ->mapWithKeys(function ($structure) {
                                                $label = $structure->feeCategory->name . ' - ' . $structure->academicYear->name;
                                                return [$structure->id => $label];
                                            });
                                    })
                                    ->required()
                                    ->reactive()
                                    ->searchable(),
                            ]),
                    ])
                    ->heading('Assignment Details'),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('custom_amount')
                                    ->label('Custom Amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->hint('Leave empty to use fee structure amount')
                                    ->reactive(),

                                Forms\Components\Select::make('discount_type')
                                    ->label('Discount Type')
                                    ->options([
                                        'percentage' => 'Percentage',
                                        'fixed' => 'Fixed Amount',
                                        'scholarship' => 'Scholarship',
                                    ])
                                    ->reactive(),

                                Forms\Components\TextInput::make('discount_percentage')
                                    ->label('Discount Percentage')
                                    ->numeric()
                                    ->suffix('%')
                                    ->visible(fn(callable $get) => $get('discount_type') === 'percentage')
                                    ->reactive(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('discount_amount')
                                    ->label('Discount Amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled(fn(callable $get) => $get('discount_type') === 'percentage')
                                    ->reactive(),

                                Forms\Components\TextInput::make('final_amount')
                                    ->label('Final Amount')
                                    ->numeric()
                                    ->disabled()
                                    ->prefix('$')
                                    ->reactive(),
                            ]),

                        Forms\Components\TextInput::make('discount_reason')
                            ->label('Discount Reason')
                            ->placeholder('e.g., Merit scholarship, Financial hardship')
                            ->visible(fn(callable $get) => $get('discount_type') !== null),
                    ])
                    ->heading('Discount Configuration'),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('assigned_date')
                                    ->label('Assigned Date')
                                    ->required()
                                    ->default(now()),

                                Forms\Components\DatePicker::make('effective_from')
                                    ->label('Effective From')
                                    ->required()
                                    ->default(now()),

                                Forms\Components\DatePicker::make('effective_till')
                                    ->label('Effective Till')
                                    ->hint('Leave empty for indefinite validity'),
                            ]),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Forms\Components\KeyValue::make('special_conditions')
                            ->label('Special Conditions')
                            ->keyLabel('Condition')
                            ->valueLabel('Value'),

                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->placeholder('Additional notes'),
                    ])
                    ->heading('Validity & Conditions'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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

                Tables\Columns\TextColumn::make('feeStructure.feeCategory.name')
                    ->label('Fee Category')
                    ->sortable(),

                Tables\Columns\TextColumn::make('feeStructure.academicYear.name')
                    ->label('Academic Year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('formatted_final_amount')
                    ->label('Final Amount')
                    ->sortable('final_amount'),

                Tables\Columns\TextColumn::make('discount_amount')
                    ->label('Discount')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'partially_paid' => 'warning',
                        'unpaid' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('assigned_date')
                    ->label('Assigned')
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('student.school_id')
                    ->label('School')
                    ->options(School::active()->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('student.class_id')
                    ->label('Class')
                    ->relationship('student.schoolClass', 'name'),

                Tables\Filters\SelectFilter::make('feeStructure.fee_category_id')
                    ->label('Fee Category')
                    ->relationship('feeStructure.feeCategory', 'name'),

                Tables\Filters\SelectFilter::make('discount_type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                        'scholarship' => 'Scholarship',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),

                Tables\Filters\Filter::make('has_discount')
                    ->label('Has Discount')
                    ->query(fn(Builder $query): Builder => $query->where('discount_amount', '>', 0)),

                Tables\Filters\Filter::make('unpaid')
                    ->label('Unpaid Fees')
                    ->query(fn(Builder $query): Builder => $query->whereHas('feeInstallments', function ($q) {
                        $q->where('status', '!=', 'paid');
                    })),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('generateInstallments')
                    ->label('Generate Installments')
                    ->icon('heroicon-o-calendar')
                    ->color('info')
                    ->action(function ($record) {
                        $record->generateInstallments();
                    })
                    ->visible(fn($record) => $record->feeInstallments()->count() === 0),
                Tables\Actions\Action::make('viewPayments')
                    ->label('View Payments')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->url(fn($record) => route('filament.admin.resources.fee-payments.index', [
                        'tableFilters' => ['student_id' => ['value' => $record->student_id]]
                    ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('generateInstallments')
                        ->label('Generate Installments')
                        ->icon('heroicon-o-calendar')
                        ->color('info')
                        ->action(function ($records) {
                            $records->each->generateInstallments();
                        }),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                        }),
                ]),
            ])
            ->defaultSort('assigned_date', 'desc');
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
            'index' => Pages\ListStudentFeeAssignments::route('/'),
            'create' => Pages\CreateStudentFeeAssignment::route('/create'),
            'view' => Pages\ViewStudentFeeAssignment::route('/{record}'),
            'edit' => Pages\EditStudentFeeAssignment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'student.user',
            'student.schoolClass',
            'feeStructure.feeCategory',
            'feeStructure.academicYear'
        ]);
    }
}
