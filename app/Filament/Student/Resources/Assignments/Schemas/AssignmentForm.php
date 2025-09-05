<?php

namespace App\Filament\Student\Resources\Assignments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;

class AssignmentForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                Select::make('teacher_id')
                    ->relationship('teacher', 'id')
                    ->required(),
                TextInput::make('class_id')
                    ->required()
                    ->numeric(),
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('instructions')
                    ->columnSpanFull(),
                DatePicker::make('due_date')
                    ->required(),
                TimePicker::make('due_time'),
                TextInput::make('max_marks')
                    ->required()
                    ->numeric()
                    ->default(100),
                TextInput::make('attachments'),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'closed' => 'Closed'])
                    ->default('draft')
                    ->required(),
            ]);
    }
}
