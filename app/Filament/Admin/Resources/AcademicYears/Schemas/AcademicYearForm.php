<?php

namespace App\Filament\Admin\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(2),

                TextInput::make('name')
                    ->label('Academic Year Name')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('e.g., 2024-2025')
                    ->columnSpan(2),

                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required()
                    ->native(false)
                    ->columnSpan(1),

                DatePicker::make('end_date')
                    ->label('End Date')
                    ->required()
                    ->native(false)
                    ->after('start_date')
                    ->columnSpan(1),

                Toggle::make('is_current')
                    ->label('Set as Current Academic Year')
                    ->helperText('Only one academic year can be current at a time.')
                    ->columnSpan(1),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive'
                    ])
                    ->default('active')
                    ->required()
                    ->columnSpan(1),
            ])
            ->columns(3);
    }
}
