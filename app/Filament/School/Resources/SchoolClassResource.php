<?php

namespace App\Filament\School\Resources;

use App\Filament\School\Resources\SchoolClassResource\Pages;
use App\Models\SchoolClass;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SchoolClassResource extends Resource
{
    protected static ?string $model = SchoolClass::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Classes';

    protected static ?string $modelLabel = 'Class';

    protected static ?string $pluralModelLabel = 'Classes';

    protected static ?string $navigationGroup = 'Academic Structure';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        return parent::getEloquentQuery()
            ->when($user && !$user->isSuperAdmin(), function (Builder $query) use ($user) {
                $query->where('school_id', $user->school_id);
            })
            ->with(['school', 'academicYear']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Class Information')
                    ->schema([
                        Forms\Components\Select::make('academic_year_id')
                            ->relationship(
                                'academicYear',
                                'name',
                                fn(Builder $query) =>
                                $query->when(Auth::user() && !Auth::user()->isSuperAdmin(), function (Builder $q) {
                                    $q->where('school_id', Auth::user()->school_id);
                                })
                            )
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('e.g., Grade 1, Class 10'),
                        Forms\Components\TextInput::make('section')
                            ->maxLength(10)
                            ->placeholder('e.g., A, B, C'),
                        Forms\Components\TextInput::make('room_number')
                            ->maxLength(20)
                            ->placeholder('e.g., 101, A-205'),
                        Forms\Components\TextInput::make('capacity')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Maximum number of students'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'completed' => 'Completed',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(500),
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
                Tables\Columns\TextColumn::make('section')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Academic Year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('room_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'completed' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->relationship('academicYear', 'name')
                    ->label('Academic Year'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'completed' => 'Completed',
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
            'index' => Pages\ListSchoolClasses::route('/'),
            'create' => Pages\CreateSchoolClass::route('/create'),
            'view' => Pages\ViewSchoolClass::route('/{record}'),
            'edit' => Pages\EditSchoolClass::route('/{record}/edit'),
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

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        $query = static::getModel()::where('status', 'active');

        if ($user && !$user->isSuperAdmin() && $user->school_id) {
            $query->where('school_id', $user->school_id);
        }

        return $query->count();
    }
}
