<?php

namespace App\Filament\School\Resources;

use App\Filament\School\Resources\AcademicYearResource\Pages;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Academic Years';

    protected static ?string $modelLabel = 'Academic Year';

    protected static ?string $pluralModelLabel = 'Academic Years';

    protected static ?string $navigationGroup = 'Academic Structure';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        return parent::getEloquentQuery()
            ->when($user && !$user->isSuperAdmin(), function (Builder $query) use ($user) {
                $query->where('school_id', $user->school_id);
            })
            ->with('school');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Academic Year Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g., 2024-2025'),
                        Forms\Components\DatePicker::make('start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->after('start_date'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'upcoming' => 'Upcoming',
                                'active' => 'Active',
                                'completed' => 'Completed',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_current')
                            ->helperText('Only one academic year can be current at a time'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'upcoming' => 'warning',
                        'active' => 'success',
                        'completed' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_current')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'upcoming' => 'Upcoming',
                        'active' => 'Active',
                        'completed' => 'Completed',
                    ]),
                Tables\Filters\TernaryFilter::make('is_current')
                    ->label('Current Year'),
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
            ->defaultSort('start_date', 'desc');
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
            'index' => Pages\ListAcademicYears::route('/'),
            'create' => Pages\CreateAcademicYear::route('/create'),
            'view' => Pages\ViewAcademicYear::route('/{record}'),
            'edit' => Pages\EditAcademicYear::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && ($user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isPrincipal());
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && ($user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isPrincipal());
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->isSuperAdmin()) return true;

        return ($user->isSchoolAdmin() || $user->isPrincipal()) &&
            $user->school_id === $record->school_id;
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->isSuperAdmin()) return true;

        return ($user->isSchoolAdmin() || $user->isPrincipal()) &&
            $user->school_id === $record->school_id;
    }

    public static function canView($record): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->isSuperAdmin()) return true;

        return ($user->isSchoolAdmin() || $user->isPrincipal()) &&
            $user->school_id === $record->school_id;
    }
}
