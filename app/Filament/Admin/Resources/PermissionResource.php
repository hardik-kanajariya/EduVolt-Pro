<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PermissionResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Permission Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label('Permission Name')
                            ->helperText('Enter a unique permission name (e.g., view_students, create_users, etc.)'),

                        Forms\Components\TextInput::make('guard_name')
                            ->default('web')
                            ->required()
                            ->maxLength(255)
                            ->label('Guard Name')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Permission Details')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->maxLength(500)
                            ->label('Description')
                            ->helperText('Optional description of what this permission allows'),
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
                    ->label('Permission Name'),

                Tables\Columns\TextColumn::make('guard_name')
                    ->searchable()
                    ->sortable()
                    ->label('Guard'),

                Tables\Columns\TextColumn::make('roles_count')
                    ->counts('roles')
                    ->label('Roles')
                    ->sortable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Direct Users')
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

                Tables\Filters\Filter::make('permission_group')
                    ->form([
                        Forms\Components\Select::make('group')
                            ->options(function () {
                                return Permission::all()
                                    ->map(function ($permission) {
                                        return explode('_', $permission->name)[1] ?? 'general';
                                    })
                                    ->unique()
                                    ->sort()
                                    ->mapWithKeys(function ($group) {
                                        return [$group => ucfirst($group)];
                                    });
                            })
                            ->label('Permission Group'),
                    ])
                    ->query(function ($query, array $data) {
                        if (!empty($data['group'])) {
                            $query->where('name', 'like', '%_' . $data['group'] . '_%');
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Permission $record) {
                        // Get current permissions to prevent deletion of essential ones
                        $essentialPermissions = [
                            'view_users',
                            'create_users',
                            'edit_users',
                            'delete_users',
                            'view_students',
                            'create_students',
                            'edit_students',
                            'delete_students',
                            'view_teachers',
                            'create_teachers',
                            'edit_teachers',
                            'delete_teachers',
                            'manage_roles_permissions',
                        ];

                        if (in_array($record->name, $essentialPermissions)) {
                            throw new \Exception('Cannot delete essential permission: ' . $record->name);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            $essentialPermissions = [
                                'view_users',
                                'create_users',
                                'edit_users',
                                'delete_users',
                                'view_students',
                                'create_students',
                                'edit_students',
                                'delete_students',
                                'view_teachers',
                                'create_teachers',
                                'edit_teachers',
                                'delete_teachers',
                                'manage_roles_permissions',
                            ];

                            foreach ($records as $record) {
                                if (in_array($record->name, $essentialPermissions)) {
                                    throw new \Exception('Cannot delete essential permission: ' . $record->name);
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
        return Auth::user()->can('view_permissions');
    }

    public static function canCreate(): bool
    {
        return Auth::user()->can('create_permissions');
    }

    public static function canView($record): bool
    {
        return Auth::user()->can('view_permissions');
    }

    public static function canEdit($record): bool
    {
        // Prevent editing essential permissions
        $essentialPermissions = [
            'create_roles',
            'view_roles',
            'edit_roles',
            'delete_roles',
            'create_permissions',
            'view_permissions',
            'edit_permissions',
            'delete_permissions',
            'create_users',
            'view_users',
            'edit_users',
            'delete_users'
        ];

        if (in_array($record->name, $essentialPermissions)) {
            return false;
        }

        return Auth::user()->can('edit_permissions');
    }

    public static function canDelete($record): bool
    {
        // Prevent deleting essential permissions
        $essentialPermissions = [
            'create_roles',
            'view_roles',
            'edit_roles',
            'delete_roles',
            'create_permissions',
            'view_permissions',
            'edit_permissions',
            'delete_permissions',
            'create_users',
            'view_users',
            'edit_users',
            'delete_users'
        ];

        if (in_array($record->name, $essentialPermissions)) {
            return false;
        }

        return Auth::user()->can('delete_permissions');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'view' => Pages\ViewPermission::route('/{record}'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
