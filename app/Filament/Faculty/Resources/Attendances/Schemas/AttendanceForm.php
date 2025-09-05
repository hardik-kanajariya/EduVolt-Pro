<?php

namespace App\Filament\Faculty\Resources\Attendances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;

class AttendanceForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                Select::make('student_id')
                    ->relationship('student', 'id')
                    ->required(),
                TextInput::make('class_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('date')
                    ->required(),
                Select::make('status')
                    ->options(['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late', 'excused' => 'Excused'])
                    ->default('present')
                    ->required(),
                Textarea::make('remarks')
                    ->columnSpanFull(),
                TextInput::make('marked_by')
                    ->required()
                    ->numeric(),
            ]);
    }
}
