<?php

namespace App\Filament\Parent\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;

class StudentForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('school_id')
                    ->required()
                    ->numeric(),
                TextInput::make('class_id')
                    ->required()
                    ->numeric(),
                TextInput::make('admission_number')
                    ->required(),
                TextInput::make('roll_number'),
                DatePicker::make('admission_date')
                    ->required(),
                TextInput::make('parent_name')
                    ->required(),
                TextInput::make('parent_phone')
                    ->tel()
                    ->required(),
                TextInput::make('parent_email')
                    ->email(),
                Textarea::make('medical_info')
                    ->columnSpanFull(),
                TextInput::make('transport_route'),
                TextInput::make('emergency_contacts'),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'transferred' => 'Transferred',
                        'graduated' => 'Graduated',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }
}
