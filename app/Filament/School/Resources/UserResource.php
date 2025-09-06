<?php

namespace App\Filament\School\Resources;

use App\Filament\School\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\School;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        // Only show users from the current school admin's school
        return parent::getEloquentQuery()
            ->where('school_id', Auth::user()->school_id)
            ->orWhere('id', Auth::user()->id); // Include current user
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('address')
                            ->maxLength(500),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Personal Details')
                    ->schema([
                        Forms\Components\DatePicker::make('date_of_birth'),

                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ]),

                        Forms\Components\Toggle::make('status')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Authentication')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->password()
                            ->same('password')
                            ->requiredWith('password'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('School & Roles')
                    ->schema([
                        Forms\Components\Select::make('school_id')
                            ->options(function () {
                                // School admin can only assign users to their own school
                                return [Auth::user()->school_id => Auth::user()->school->name];
                            })
                            ->default(Auth::user()->school_id)
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->options(function () {
                                // Only show roles that school admin can assign
                                $allowedRoles = ['principal', 'teacher', 'accountant', 'librarian', 'student', 'parent'];
                                return Role::whereIn('name', $allowedRoles)->pluck('name', 'id');
                            })
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name))
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->separator(',')
                    ->colors([
                        'primary' => 'principal',
                        'success' => 'teacher',
                        'warning' => 'accountant',
                        'info' => 'librarian',
                        'secondary' => 'student',
                        'danger' => 'parent',
                    ]),

                Tables\Columns\BooleanColumn::make('status')
                    ->label('Active'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),

                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (User $record) {
                        // Prevent deletion of current user and super admins
                        if ($record->id === Auth::user()->id) {
                            throw new \Exception('Cannot delete your own account');
                        }
                        if ($record->hasRole('super_admin')) {
                            throw new \Exception('Cannot delete super admin users');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->id === Auth::user()->id) {
                                    throw new \Exception('Cannot delete your own account');
                                }
                                if ($record->hasRole('super_admin')) {
                                    throw new \Exception('Cannot delete super admin users');
                                }
                            }
                        }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
