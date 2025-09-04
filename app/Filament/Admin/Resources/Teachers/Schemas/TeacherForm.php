<?php

namespace App\Filament\Admin\Resources\Teachers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User Account')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(2),

                Select::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(2),

                TextInput::make('employee_id')
                    ->label('Employee ID')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->columnSpan(1),

                TextInput::make('qualification')
                    ->label('Highest Qualification')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Masters in Mathematics')
                    ->columnSpan(1),

                TextInput::make('experience_years')
                    ->label('Years of Experience')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(50)
                    ->columnSpan(1),

                DatePicker::make('join_date')
                    ->label('Joining Date')
                    ->required()
                    ->native(false)
                    ->columnSpan(1),

                TextInput::make('salary')
                    ->label('Monthly Salary')
                    ->numeric()
                    ->prefix('₹')
                    ->placeholder('50000')
                    ->columnSpan(1),

                Select::make('employment_type')
                    ->label('Employment Type')
                    ->options([
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract' => 'Contract',
                    ])
                    ->default('full_time')
                    ->required()
                    ->columnSpan(1),

                Textarea::make('specialization')
                    ->label('Specialization/Subject Areas')
                    ->rows(3)
                    ->placeholder('Mathematics, Physics, Science Lab Management')
                    ->columnSpanFull(),

                Repeater::make('certifications')
                    ->label('Certifications')
                    ->schema([
                        TextInput::make('name')
                            ->label('Certification Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('authority')
                            ->label('Issuing Authority')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('issue_date')
                            ->label('Issue Date')
                            ->native(false),
                        DatePicker::make('expiry_date')
                            ->label('Expiry Date (if any)')
                            ->native(false),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->defaultItems(0)
                    ->reorderable(),

                Select::make('status')
                    ->label('Employment Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'terminated' => 'Terminated'
                    ])
                    ->default('active')
                    ->required()
                    ->columnSpan(1),
            ])
            ->columns(3);
    }
}
