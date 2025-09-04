<?php

namespace App\Filament\Admin\Resources\Teachers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('school_id')
                    ->required()
                    ->numeric(),
                TextInput::make('employee_id')
                    ->required(),
                TextInput::make('qualification')
                    ->required(),
                TextInput::make('experience_years')
                    ->required()
                    ->numeric()
                    ->default(0),
                DatePicker::make('join_date')
                    ->required(),
                TextInput::make('salary')
                    ->numeric(),
                TextInput::make('employment_type')
                    ->required()
                    ->default('full_time'),
                Textarea::make('specialization')
                    ->columnSpanFull(),
                TextInput::make('certifications'),
                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive', 'terminated' => 'Terminated'])
                    ->default('active')
                    ->required(),
            ]);
    }
}
