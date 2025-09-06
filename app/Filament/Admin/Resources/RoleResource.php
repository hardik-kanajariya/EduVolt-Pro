<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Role Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label('Role Name')
                            ->helperText('Enter a unique role name (e.g., school_admin, principal, etc.)'),

                        Forms\Components\TextInput::make('guard_name')
                            ->default('web')
                            ->required()
                            ->maxLength(255)
                            ->label('Guard Name')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Permissions')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->options(function () {
                                return Permission::all()->groupBy(function ($permission) {
                                    return explode('_', $permission->name)[1] ?? 'general';
                                })->map(function ($group) {
                                    return $group->pluck('name', 'name');
                                });
                            })
                            ->columns(3)
                            ->searchable()
                            ->label('Assign Permissions')
                            ->helperText('Select permissions for this role'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Role Name'),

                Tables\Columns\TextColumn::make('guard_name')
                    ->searchable()
                    ->sortable()
                    ->label('Guard'),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissions')
                    ->sortable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Users')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('guard_name')
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Role $record) {
                        // Prevent deletion of system roles
                        $systemRoles = ['super_admin', 'school_admin', 'principal', 'teacher', 'student', 'parent'];
                        if (in_array($record->name, $systemRoles)) {
                            throw new \Exception('Cannot delete system role: ' . $record->name);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            $systemRoles = ['super_admin', 'school_admin', 'principal', 'teacher', 'student', 'parent'];
                            foreach ($records as $record) {
                                if (in_array($record->name, $systemRoles)) {
                                    throw new \Exception('Cannot delete system role: ' . $record->name);
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

    public static function canViewAny(): bool
    {
        return Auth::user()->can('view_roles');
    }

    public static function canCreate(): bool
    {
        return Auth::user()->can('create_roles');
    }

    public static function canView($record): bool
    {
        return Auth::user()->can('view_roles');
    }

    public static function canEdit($record): bool
    {
        // Prevent editing system roles
        $systemRoles = ['super_admin', 'school_admin', 'principal', 'teacher', 'student', 'parent', 'accountant', 'librarian'];
        if (in_array($record->name, $systemRoles)) {
            return false;
        }

        return Auth::user()->can('edit_roles');
    }

    public static function canDelete($record): bool
    {
        // Prevent deleting system roles
        $systemRoles = ['super_admin', 'school_admin', 'principal', 'teacher', 'student', 'parent', 'accountant', 'librarian'];
        if (in_array($record->name, $systemRoles)) {
            return false;
        }

        return Auth::user()->can('delete_roles');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
