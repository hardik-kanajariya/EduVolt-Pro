<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FeeCategoryResource\Pages;
use App\Models\FeeCategory;
use App\Models\School;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FeeCategoryResource extends Resource
{
    protected static ?string $model = FeeCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Fee Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Fee Categories';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('school_id')
                                    ->label('School')
                                    ->options(School::active()->pluck('name', 'id'))
                                    ->required()
                                    ->reactive()
                                    ->searchable(),

                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Tuition Fee'),

                                Forms\Components\TextInput::make('code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50)
                                    ->placeholder('e.g., TUITION')
                                    ->hint('Unique identifier for this fee category'),

                                Forms\Components\Select::make('type')
                                    ->options([
                                        'mandatory' => 'Mandatory',
                                        'optional' => 'Optional',
                                        'conditional' => 'Conditional',
                                    ])
                                    ->default('mandatory')
                                    ->required(),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->placeholder('Brief description of this fee category'),
                    ]),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('frequency')
                                    ->options([
                                        'monthly' => 'Monthly',
                                        'quarterly' => 'Quarterly',
                                        'half_yearly' => 'Half Yearly',
                                        'yearly' => 'Yearly',
                                        'one_time' => 'One Time',
                                    ])
                                    ->default('monthly')
                                    ->required(),

                                Forms\Components\TextInput::make('due_day')
                                    ->label('Due Day of Month')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->maxValue(31)
                                    ->hint('Day of month when payment is due'),

                                Forms\Components\Toggle::make('is_recurring')
                                    ->label('Recurring Fee')
                                    ->default(true)
                                    ->hint('Whether this fee repeats based on frequency'),
                            ]),
                    ])
                    ->heading('Payment Schedule'),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('late_fee_amount')
                                    ->label('Late Fee Amount')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$'),

                                Forms\Components\Select::make('late_fee_type')
                                    ->label('Late Fee Type')
                                    ->options([
                                        'fixed' => 'Fixed Amount',
                                        'percentage' => 'Percentage',
                                    ])
                                    ->default('fixed'),

                                Forms\Components\TextInput::make('late_fee_days')
                                    ->label('Grace Period (Days)')
                                    ->numeric()
                                    ->default(7)
                                    ->hint('Days after due date before late fee applies'),
                            ]),
                    ])
                    ->heading('Late Fee Configuration'),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\CheckboxList::make('applicable_classes')
                            ->label('Applicable to Classes')
                            ->options(function (callable $get) {
                                $schoolId = $get('school_id');
                                if (!$schoolId) {
                                    return [];
                                }

                                return \App\Models\SchoolClass::where('school_id', $schoolId)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->hint('Leave empty to apply to all classes')
                            ->columns(3),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),

                                Forms\Components\KeyValue::make('settings')
                                    ->label('Additional Settings')
                                    ->keyLabel('Setting')
                                    ->valueLabel('Value')
                                    ->hint('Custom key-value pairs for additional configuration'),
                            ]),
                    ])
                    ->heading('Configuration'),
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

                Tables\Columns\TextColumn::make('name')
                    ->label('Category Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->sortable()
                    ->searchable()
                    ->copyable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'mandatory',
                        'warning' => 'optional',
                        'secondary' => 'conditional',
                    ]),

                Tables\Columns\BadgeColumn::make('frequency')
                    ->colors([
                        'success' => 'monthly',
                        'info' => 'quarterly',
                        'warning' => 'half_yearly',
                        'primary' => 'yearly',
                        'secondary' => 'one_time',
                    ]),

                Tables\Columns\TextColumn::make('due_day')
                    ->label('Due Day')
                    ->suffix(fn($record) => match ($record->due_day) {
                        1, 21, 31 => 'st',
                        2, 22 => 'nd',
                        3, 23 => 'rd',
                        default => 'th'
                    }),

                Tables\Columns\TextColumn::make('formatted_late_fees')
                    ->label('Late Fee'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school_id')
                    ->label('School')
                    ->options(School::active()->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'mandatory' => 'Mandatory',
                        'optional' => 'Optional',
                        'conditional' => 'Conditional',
                    ]),

                Tables\Filters\SelectFilter::make('frequency')
                    ->options([
                        'monthly' => 'Monthly',
                        'quarterly' => 'Quarterly',
                        'half_yearly' => 'Half Yearly',
                        'yearly' => 'Yearly',
                        'one_time' => 'One Time',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            ->defaultSort('name');
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
            'index' => Pages\ListFeeCategories::route('/'),
            'create' => Pages\CreateFeeCategory::route('/create'),
            'view' => Pages\ViewFeeCategory::route('/{record}'),
            'edit' => Pages\EditFeeCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['school']);
    }
}
