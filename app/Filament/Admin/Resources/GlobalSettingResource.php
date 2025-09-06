<?php

namespace App\Filament\Admin\Resources;

use App\Models\GlobalSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;

class GlobalSettingResource extends Resource
{
    protected static ?string $model = GlobalSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'System Configuration';

    protected static ?string $navigationLabel = 'Global Settings';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Setting Details')
                    ->description('Configure global system settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('key')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('e.g., site_name, email_from')
                                    ->helperText('Unique identifier for this setting'),

                                Forms\Components\Select::make('type')
                                    ->required()
                                    ->options([
                                        'string' => 'String',
                                        'boolean' => 'Boolean',
                                        'integer' => 'Integer',
                                        'array' => 'Array',
                                        'object' => 'Object',
                                    ]),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(500)
                            ->rows(2)
                            ->placeholder('Brief description of what this setting controls'),

                        Forms\Components\Toggle::make('is_public')
                            ->helperText('Public settings are accessible in frontend applications'),

                        Forms\Components\Textarea::make('value_json')
                            ->placeholder('Enter value in JSON format, e.g., "string value", true, 123, ["array"], {"key": "value"}')
                            ->helperText('Enter the setting value in JSON format')
                            ->required()
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'string' => 'primary',
                        'boolean' => 'success',
                        'integer' => 'info',
                        'array' => 'warning',
                        'object' => 'danger',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('value')
                    ->limit(30)
                    ->formatStateUsing(function ($state, $record) {
                        if (is_array($state)) {
                            return json_encode($state);
                        }
                        return (string) $state;
                    })
                    ->tooltip(function (Tables\Columns\TextColumn $column, $record): ?string {
                        $value = $record->getValue();
                        if (is_array($value)) {
                            return json_encode($value, JSON_PRETTY_PRINT);
                        }
                        return (string) $value;
                    }),

                Tables\Columns\IconColumn::make('is_public')
                    ->boolean(),

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
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'string' => 'String',
                        'boolean' => 'Boolean',
                        'integer' => 'Integer',
                        'array' => 'Array',
                        'object' => 'Object',
                    ]),

                Tables\Filters\TernaryFilter::make('is_public')
                    ->placeholder('All settings')
                    ->trueLabel('Public only')
                    ->falseLabel('Private only'),
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
            ->defaultSort('key');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\GlobalSettingResource\Pages\ListGlobalSettings::route('/'),
            'create' => \App\Filament\Admin\Resources\GlobalSettingResource\Pages\CreateGlobalSetting::route('/create'),
            'edit' => \App\Filament\Admin\Resources\GlobalSettingResource\Pages\EditGlobalSetting::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Only super admins can access global settings
        return parent::getEloquentQuery();
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
