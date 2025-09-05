<?php

namespace App\Filament\Admin\Resources\ExamSubjects\Schemas;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;

class ExamSubjectForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                Select::make('exam_id')
                    ->label('Exam')
                    ->relationship('exam', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                DatePicker::make('exam_date')
                    ->required()
                    ->native(false),

                TimePicker::make('start_time')
                    ->required()
                    ->native(false),

                TimePicker::make('end_time')
                    ->required()
                    ->native(false)
                    ->after('start_time'),

                TextInput::make('duration_minutes')
                    ->numeric()
                    ->required()
                    ->default(180)
                    ->suffix('minutes'),

                TextInput::make('room')
                    ->maxLength(255)
                    ->placeholder('e.g., Room 101, Lab A'),

                Select::make('teacher_id')
                    ->label('Supervising Teacher')
                    ->relationship('teacher', 'user.name')
                    ->searchable()
                    ->preload(),

                TextInput::make('max_marks')
                    ->numeric()
                    ->required()
                    ->default(100)
                    ->suffix('marks'),

                TextInput::make('theory_marks')
                    ->numeric()
                    ->default(80)
                    ->suffix('marks'),

                TextInput::make('practical_marks')
                    ->numeric()
                    ->default(20)
                    ->suffix('marks'),

                Textarea::make('instructions')
                    ->rows(3)
                    ->placeholder('Special instructions for this subject exam'),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                Toggle::make('is_completed')
                    ->label('Completed')
                    ->default(false),
            ]);
    }
}
