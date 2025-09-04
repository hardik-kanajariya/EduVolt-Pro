<?php

namespace App\Filament\Admin\Resources\SchoolClasses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SchoolClassForm
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

                Select::make('academic_year_id')
                    ->label('Academic Year')
                    ->relationship('academicYear', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(2),

                TextInput::make('name')
                    ->label('Class Name')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('e.g., Grade 1, Class 10')
                    ->columnSpan(1),

                TextInput::make('section')
                    ->label('Section')
                    ->required()
                    ->maxLength(10)
                    ->default('A')
                    ->placeholder('e.g., A, B, C')
                    ->columnSpan(1),

                TextInput::make('capacity')
                    ->label('Student Capacity')
                    ->required()
                    ->numeric()
                    ->default(30)
                    ->minValue(1)
                    ->maxValue(100)
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
