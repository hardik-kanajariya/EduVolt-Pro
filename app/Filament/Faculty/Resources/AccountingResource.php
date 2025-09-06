<?php

namespace App\Filament\Faculty\Resources;

use App\Filament\Faculty\Resources\AccountingResource\Pages;
use App\Models\SchoolFinance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class AccountingResource extends Resource
{
    protected static ?string $model = SchoolFinance::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Financial Records';

    protected static ?string $slug = 'financial-records';

    protected static ?string $navigationGroup = 'Financial Management';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if (!$user || !$user->hasAnyRole(['accountant', 'school_admin', 'principal'])) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('school_id', $user->school_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                    ])
                    ->required(),

                Select::make('category')
                    ->options([
                        'tuition_fee' => 'Tuition Fee',
                        'admission_fee' => 'Admission Fee',
                        'library_fee' => 'Library Fee',
                        'lab_fee' => 'Lab Fee',
                        'transport_fee' => 'Transport Fee',
                        'examination_fee' => 'Examination Fee',
                        'salary' => 'Salary',
                        'utilities' => 'Utilities',
                        'maintenance' => 'Maintenance',
                        'supplies' => 'Supplies',
                        'equipment' => 'Equipment',
                        'other' => 'Other',
                    ])
                    ->required(),

                TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->rows(3),

                DatePicker::make('transaction_date')
                    ->required(),

                TextInput::make('reference_number')
                    ->maxLength(100),

                Select::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'bank_transfer' => 'Bank Transfer',
                        'cheque' => 'Cheque',
                        'online' => 'Online Payment',
                        'card' => 'Card Payment',
                    ]),

                Select::make('student_id')
                    ->relationship('student', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user) {
                            $query->where('school_id', $user->school_id);
                        }
                    })
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_date')
                    ->date()
                    ->sortable(),

                BadgeColumn::make('type')
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                    ]),

                TextColumn::make('category')
                    ->formatStateUsing(fn($state) => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('student.name')
                    ->searchable(),

                TextColumn::make('payment_method')
                    ->formatStateUsing(fn($state) => ucfirst(str_replace('_', ' ', $state))),

                TextColumn::make('reference_number')
                    ->searchable(),

                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                    ]),

                SelectFilter::make('category')
                    ->options([
                        'tuition_fee' => 'Tuition Fee',
                        'admission_fee' => 'Admission Fee',
                        'library_fee' => 'Library Fee',
                        'lab_fee' => 'Lab Fee',
                        'transport_fee' => 'Transport Fee',
                        'examination_fee' => 'Examination Fee',
                        'salary' => 'Salary',
                        'utilities' => 'Utilities',
                        'maintenance' => 'Maintenance',
                        'supplies' => 'Supplies',
                        'equipment' => 'Equipment',
                        'other' => 'Other',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'bank_transfer' => 'Bank Transfer',
                        'cheque' => 'Cheque',
                        'online' => 'Online Payment',
                        'card' => 'Card Payment',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListAccounting::route('/'),
            'create' => Pages\CreateAccounting::route('/create'),
            'view' => Pages\ViewAccounting::route('/{record}'),
            'edit' => Pages\EditAccounting::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->hasAnyRole(['accountant', 'school_admin', 'principal']);
    }
}
