<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FeeStructureResource\Pages;
use App\Models\FeeStructure;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\FeeCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FeeStructureResource extends Resource
{
    protected static ?string $model = FeeStructure::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Fee Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Fee Structure';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('school_id')
                                    ->label('School')
                                    ->options(School::active()->pluck('name', 'id'))
                                    ->required()
                                    ->reactive()
                                    ->searchable(),

                                Forms\Components\Select::make('academic_year_id')
                                    ->label('Academic Year')
                                    ->options(function (callable $get) {
                                        $schoolId = $get('school_id');
                                        if (!$schoolId) {
                                            return [];
                                        }
                                        return AcademicYear::where('school_id', $schoolId)
                                            ->orderBy('name', 'desc')
                                            ->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->reactive()
                                    ->searchable(),

                                Forms\Components\Select::make('class_id')
                                    ->label('Class')
                                    ->options(function (callable $get) {
                                        $schoolId = $get('school_id');
                                        if (!$schoolId) {
                                            return [];
                                        }
                                        return SchoolClass::where('school_id', $schoolId)
                                            ->orderBy('name')
                                            ->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->reactive()
                                    ->searchable(),

                                Forms\Components\Select::make('fee_category_id')
                                    ->label('Fee Category')
                                    ->options(function (callable $get) {
                                        $schoolId = $get('school_id');
                                        $classId = $get('class_id');
                                        if (!$schoolId || !$classId) {
                                            return [];
                                        }
                                        return FeeCategory::where('school_id', $schoolId)
                                            ->active()
                                            ->forClass($classId)
                                            ->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->searchable(),
                            ]),
                    ]),

                Forms\Components\Section::make('Fee Details')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->label('Base Amount')
                                    ->numeric()
                                    ->required()
                                    ->prefix('$')
                                    ->reactive(),

                                Forms\Components\TextInput::make('discount_amount')
                                    ->label('Discount Amount')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$')
                                    ->reactive(),

                                Forms\Components\TextInput::make('additional_charges')
                                    ->label('Additional Charges')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$')
                                    ->reactive(),
                            ]),

                        Forms\Components\TextInput::make('final_amount')
                            ->label('Final Amount')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->prefix('$')
                            ->reactive()
                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, callable $get) {
                                $amount = $get('amount') ?? 0;
                                $discount = $get('discount_amount') ?? 0;
                                $additional = $get('additional_charges') ?? 0;
                                $component->state($amount - $discount + $additional);
                            }),
                    ]),

                Forms\Components\Section::make('Variable Amounts')
                    ->schema([
                        Forms\Components\KeyValue::make('month_wise_amounts')
                            ->label('Month-wise Amounts')
                            ->keyLabel('Month')
                            ->valueLabel('Amount')
                            ->hint('Optional: Specify different amounts for different months')
                            ->columnSpan('full'),
                    ]),

                Forms\Components\Section::make('Validity & Status')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
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

                        Forms\Components\KeyValue::make('conditions')
                            ->label('Special Conditions')
                            ->keyLabel('Condition')
                            ->valueLabel('Value')
                            ->hint('Define special conditions for this fee structure'),

                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->placeholder('Additional notes or comments'),
                    ])
                    ->heading('Validity & Conditions'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('school.name')
                    ->label('School')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Academic Year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->sortable(),

                Tables\Columns\TextColumn::make('feeCategory.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('formatted_amount')
                    ->label('Base Amount')
                    ->sortable('amount'),

                Tables\Columns\TextColumn::make('discount_amount')
                    ->label('Discount')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('formatted_final_amount')
                    ->label('Final Amount')
                    ->sortable('final_amount'),

                Tables\Columns\TextColumn::make('effective_from')
                    ->label('From')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('effective_till')
                    ->label('Till')
                    ->date()
                    ->sortable()
                    ->default('∞'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school_id')
                    ->label('School')
                    ->options(School::active()->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Academic Year')
                    ->options(AcademicYear::orderBy('name', 'desc')->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('Class')
                    ->options(SchoolClass::orderBy('name')->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('fee_category_id')
                    ->label('Fee Category')
                    ->options(FeeCategory::active()->pluck('name', 'id')),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),

                Tables\Filters\Filter::make('effective_now')
                    ->label('Effective Now')
                    ->query(fn(Builder $query): Builder => $query->effective()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('secondary')
                    ->action(function ($record) {
                        $newRecord = $record->replicate();
                        $newRecord->save();
                        return redirect()->to(static::getUrl('edit', ['record' => $newRecord]));
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                        }),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListFeeStructures::route('/'),
            'create' => Pages\CreateFeeStructure::route('/create'),
            'view' => Pages\ViewFeeStructure::route('/{record}'),
            'edit' => Pages\EditFeeStructure::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['school', 'academicYear', 'schoolClass', 'feeCategory']);
    }
}
