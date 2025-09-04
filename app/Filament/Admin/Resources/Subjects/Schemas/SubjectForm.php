<?php

namespace App\Filament\Admin\Resources\Subjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SubjectForm
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
                    ->label('Subject Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Mathematics, Physics, English')
                    ->columnSpan(1),

                TextInput::make('code')
                    ->label('Subject Code')
                    ->maxLength(20)
                    ->placeholder('e.g., MATH101, PHY201')
                    ->columnSpan(1),

                Select::make('type')
                    ->label('Subject Type')
                    ->options([
                        'core' => 'Core Subject',
                        'elective' => 'Elective Subject',
                        'extra_curricular' => 'Extra-Curricular',
                    ])
                    ->default('core')
                    ->required()
                    ->columnSpan(1),

                TextInput::make('credits')
                    ->label('Credit Hours')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->maxValue(10)
                    ->columnSpan(1),

                Textarea::make('description')
                    ->label('Subject Description')
                    ->rows(3)
                    ->placeholder('Brief description of the subject curriculum and objectives')
                    ->columnSpanFull(),

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
