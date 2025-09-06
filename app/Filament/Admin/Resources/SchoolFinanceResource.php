<?php

namespace App\Filament\Admin\Resources;

use App\Models\SchoolFinance;
use App\Models\School;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SchoolFinanceResource extends Resource
{
    protected static ?string $model = SchoolFinance::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Financial Overview';

    protected static ?string $navigationLabel = 'School Finances';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Financial Details')
                    ->description('Manage school financial data')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('school_id')
                                    ->relationship('school', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('month_year')
                                    ->required()
                                    ->placeholder('YYYY-MM')
                                    ->helperText('Format: 2024-09'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('revenue')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('₹'),

                                Forms\Components\TextInput::make('expenses')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('₹'),

                                Forms\Components\TextInput::make('profit_loss')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('₹')
                                    ->readOnly(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('fee_collection')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('₹'),

                                Forms\Components\TextInput::make('salary_expenses')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('₹'),

                                Forms\Components\TextInput::make('operational_expenses')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('₹'),
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('school.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('month_year')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('revenue')
                    ->money('INR')
                    ->sortable()
                    ->color('success'),

                Tables\Columns\TextColumn::make('expenses')
                    ->money('INR')
                    ->sortable()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('profit_loss')
                    ->money('INR')
                    ->sortable()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('fee_collection')
                    ->money('INR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school_id')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('month_year')
                    ->form([
                        Forms\Components\DatePicker::make('from_month')
                            ->placeholder('From Month'),
                        Forms\Components\DatePicker::make('to_month')
                            ->placeholder('To Month'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_month'],
                                fn(Builder $query, $date): Builder => $query->where('month_year', '>=', Carbon::parse($date)->format('Y-m')),
                            )
                            ->when(
                                $data['to_month'],
                                fn(Builder $query, $date): Builder => $query->where('month_year', '<=', Carbon::parse($date)->format('Y-m')),
                            );
                    }),

                Tables\Filters\Filter::make('profitable')
                    ->query(fn(Builder $query): Builder => $query->where('profit_loss', '>', 0)),

                Tables\Filters\Filter::make('loss_making')
                    ->query(fn(Builder $query): Builder => $query->where('profit_loss', '<', 0)),
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
            ])
            ->defaultSort('month_year', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\SchoolFinanceResource\Pages\ListSchoolFinances::route('/'),
            'create' => \App\Filament\Admin\Resources\SchoolFinanceResource\Pages\CreateSchoolFinance::route('/create'),
            'edit' => \App\Filament\Admin\Resources\SchoolFinanceResource\Pages\EditSchoolFinance::route('/{record}/edit'),
            'view' => \App\Filament\Admin\Resources\SchoolFinanceResource\Pages\ViewSchoolFinance::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Super admins can see all school finances
        return parent::getEloquentQuery()->with(['school']);
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }
}
