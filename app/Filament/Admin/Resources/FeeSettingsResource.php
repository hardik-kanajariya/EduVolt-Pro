<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FeeSettingsResource\Pages;
use App\Models\FeeSettings;
use App\Models\School;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FeeSettingsResource extends Resource
{
    protected static ?string $model = FeeSettings::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Fee Management';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationLabel = 'Fee Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('school_id')
                            ->label('School')
                            ->options(School::active()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->heading('School Selection'),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('currency')
                                    ->label('Currency Code')
                                    ->placeholder('USD, EUR, INR, etc.')
                                    ->maxLength(3)
                                    ->required(),

                                Forms\Components\TextInput::make('currency_symbol')
                                    ->label('Currency Symbol')
                                    ->placeholder('$, €, ₹, etc.')
                                    ->maxLength(5)
                                    ->required(),

                                Forms\Components\TextInput::make('decimal_places')
                                    ->label('Decimal Places')
                                    ->numeric()
                                    ->default(2)
                                    ->minValue(0)
                                    ->maxValue(4)
                                    ->required(),
                            ]),
                    ])
                    ->heading('Currency Settings'),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('receipt_prefix')
                                    ->label('Receipt Prefix')
                                    ->placeholder('REC, RCPT, etc.')
                                    ->maxLength(10)
                                    ->default('REC')
                                    ->required(),

                                Forms\Components\TextInput::make('receipt_number_length')
                                    ->label('Receipt Number Length')
                                    ->numeric()
                                    ->default(6)
                                    ->minValue(4)
                                    ->maxValue(10)
                                    ->required(),

                                Forms\Components\TextInput::make('last_receipt_number')
                                    ->label('Last Receipt Number')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->hint('Auto-incremented'),
                            ]),
                    ])
                    ->heading('Receipt Configuration'),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('auto_generate_installments')
                                    ->label('Auto Generate Installments')
                                    ->default(true)
                                    ->hint('Automatically create installments for fee assignments'),

                                Forms\Components\TextInput::make('default_installments')
                                    ->label('Default Installments')
                                    ->numeric()
                                    ->default(12)
                                    ->minValue(1)
                                    ->maxValue(24)
                                    ->hint('Number of installments for monthly fees'),

                                Forms\Components\TextInput::make('grace_period_days')
                                    ->label('Grace Period (Days)')
                                    ->numeric()
                                    ->default(7)
                                    ->minValue(0)
                                    ->maxValue(30)
                                    ->hint('Days before late fee applies'),
                            ]),
                    ])
                    ->heading('Installment Settings'),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('default_late_fee')
                                    ->label('Default Late Fee')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$')
                                    ->hint('Default late fee amount'),

                                Forms\Components\Select::make('late_fee_calculation')
                                    ->label('Late Fee Calculation')
                                    ->options([
                                        'per_day' => 'Per Day',
                                        'fixed' => 'Fixed Amount',
                                        'percentage' => 'Percentage',
                                    ])
                                    ->default('fixed')
                                    ->required(),

                                Forms\Components\Toggle::make('enable_partial_payments')
                                    ->label('Enable Partial Payments')
                                    ->default(true),
                            ]),

                        Forms\Components\Toggle::make('enable_advance_payments')
                            ->label('Enable Advance Payments')
                            ->default(true)
                            ->hint('Allow payments before due date'),
                    ])
                    ->heading('Payment Settings'),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TagsInput::make('reminder_schedule')
                            ->label('Reminder Schedule (Days)')
                            ->placeholder('Enter days before due date')
                            ->hint('e.g., 7, 3, 1 - sends reminders 7, 3, and 1 days before due date')
                            ->default(['7', '3', '1']),
                    ])
                    ->heading('Reminder Settings'),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\KeyValue::make('receipt_template_settings')
                            ->label('Receipt Template Settings')
                            ->keyLabel('Setting')
                            ->valueLabel('Value')
                            ->hint('Custom settings for receipt template'),

                        Forms\Components\KeyValue::make('notification_settings')
                            ->label('Notification Settings')
                            ->keyLabel('Setting')
                            ->valueLabel('Value')
                            ->hint('Email and SMS notification configurations'),
                    ])
                    ->heading('Advanced Settings')
                    ->collapsible(),
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

                Tables\Columns\TextColumn::make('formatted_currency')
                    ->label('Currency')
                    ->sortable('currency'),

                Tables\Columns\TextColumn::make('receipt_prefix')
                    ->label('Receipt Prefix')
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_receipt_number')
                    ->label('Last Receipt No.')
                    ->sortable(),

                Tables\Columns\IconColumn::make('auto_generate_installments')
                    ->label('Auto Installments')
                    ->boolean(),

                Tables\Columns\TextColumn::make('default_installments')
                    ->label('Default Installments')
                    ->sortable(),

                Tables\Columns\TextColumn::make('grace_period_days')
                    ->label('Grace Period')
                    ->suffix(' days')
                    ->sortable(),

                Tables\Columns\IconColumn::make('enable_partial_payments')
                    ->label('Partial Payments')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school_id')
                    ->label('School')
                    ->options(School::active()->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('currency')
                    ->options([
                        'USD' => 'USD ($)',
                        'EUR' => 'EUR (€)',
                        'INR' => 'INR (₹)',
                        'GBP' => 'GBP (£)',
                    ]),

                Tables\Filters\TernaryFilter::make('auto_generate_installments')
                    ->label('Auto Generate Installments'),

                Tables\Filters\TernaryFilter::make('enable_partial_payments')
                    ->label('Partial Payments Enabled'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('school.name');
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
            'index' => Pages\ListFeeSettings::route('/'),
            'create' => Pages\CreateFeeSettings::route('/create'),
            'view' => Pages\ViewFeeSettings::route('/{record}'),
            'edit' => Pages\EditFeeSettings::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['school']);
    }
}
