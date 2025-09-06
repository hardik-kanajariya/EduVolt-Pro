<?php

namespace App\Filament\School\Resources;

use App\Filament\School\Resources\StaffResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Staff';

    protected static ?string $modelLabel = 'Staff Member';

    protected static ?string $pluralModelLabel = 'Staff Members';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        return parent::getEloquentQuery()
            ->whereHas('roles', function (Builder $query) {
                $query->whereIn('name', ['principal', 'accountant', 'librarian']);
            })
            ->when($user && !$user->isSuperAdmin(), function (Builder $query) use ($user) {
                $query->where('school_id', $user->school_id);
            })
            ->with(['school', 'roles']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date_of_birth'),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ]),
                        Forms\Components\Textarea::make('address')
                            ->maxLength(500),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Professional Information')
                    ->schema([
                        Forms\Components\TextInput::make('employee_id')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->options([
                                'principal' => 'Principal',
                                'accountant' => 'Accountant',
                                'librarian' => 'Librarian',
                            ])
                            ->required()
                            ->preload(),
                        Forms\Components\DatePicker::make('joining_date'),
                        Forms\Components\TextInput::make('salary')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Textarea::make('qualifications')
                            ->maxLength(1000),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Account Settings')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn(string $context): bool => $context === 'create'),
                        Forms\Components\Toggle::make('is_active'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id')
                    ->label('Employee ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->getStateUsing(fn(User $record): string => $record->first_name . ' ' . $record->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'principal' => 'success',
                        'accountant' => 'warning',
                        'librarian' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('joining_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Role')
                    ->options([
                        'principal' => 'Principal',
                        'accountant' => 'Accountant',
                        'librarian' => 'Librarian',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status'),
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
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'view' => Pages\ViewStaff::route('/{record}'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
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
