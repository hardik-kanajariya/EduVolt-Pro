<?php

namespace App\Filament\Student\Resources\Grades\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class GradeForm
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
                TextInput::make('class_id')
                    ->required()
                    ->numeric(),
                TextInput::make('exam_type')
                    ->required(),
                TextInput::make('exam_name')
                    ->required(),
                TextInput::make('obtained_marks')
                    ->required()
                    ->numeric(),
                TextInput::make('total_marks')
                    ->required()
                    ->numeric(),
                TextInput::make('percentage')
                    ->required()
                    ->numeric(),
                TextInput::make('grade'),
                Textarea::make('remarks')
                    ->columnSpanFull(),
                DatePicker::make('exam_date')
                    ->required(),
            ]);
    }
}
