<?php

namespace App\Filament\Parent\Resources\StudentProgress\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StudentProgressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->relationship('student', 'id')
                    ->required(),
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required(),
                TextInput::make('term')
                    ->required(),
                TextInput::make('academic_year')
                    ->required(),
                TextInput::make('attendance_percentage')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('assignment_average')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('exam_average')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('overall_grade')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('letter_grade'),
                Textarea::make('teacher_remarks')
                    ->columnSpanFull(),
                Select::make('conduct')
                    ->options([
            'excellent' => 'Excellent',
            'good' => 'Good',
            'satisfactory' => 'Satisfactory',
            'needs_improvement' => 'Needs improvement',
        ])
                    ->default('good')
                    ->required(),
            ]);
    }
}
