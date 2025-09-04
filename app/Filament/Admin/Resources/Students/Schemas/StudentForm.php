<?php

namespace App\Filament\Admin\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Select User Account')
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
                    ->columnSpan(1),

                Select::make('class_id')
                    ->label('Class')
                    ->relationship('schoolClass', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(1),

                TextInput::make('admission_number')
                    ->label('Admission Number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->columnSpan(1),

                TextInput::make('roll_number')
                    ->label('Roll Number')
                    ->maxLength(20)
                    ->columnSpan(1),

                DatePicker::make('admission_date')
                    ->label('Admission Date')
                    ->required()
                    ->native(false)
                    ->columnSpan(1),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'transferred' => 'Transferred',
                        'graduated' => 'Graduated',
                    ])
                    ->default('active')
                    ->required()
                    ->columnSpan(1),

                TextInput::make('parent_name')
                    ->label('Parent/Guardian Name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),

                TextInput::make('parent_phone')
                    ->label('Parent Phone')
                    ->tel()
                    ->required()
                    ->maxLength(20)
                    ->columnSpan(1),

                TextInput::make('parent_email')
                    ->label('Parent Email')
                    ->email()
                    ->maxLength(255)
                    ->columnSpan(1),

                Textarea::make('medical_info')
                    ->label('Medical Information')
                    ->rows(3)
                    ->columnSpanFull(),

                TextInput::make('transport_route')
                    ->label('Transport Route')
                    ->maxLength(255)
                    ->columnSpan(1),

                Textarea::make('emergency_contacts')
                    ->label('Emergency Contacts')
                    ->rows(3)
                    ->columnSpan(1),
            ])
            ->columns(3);
    }
}
