<?php

namespace App\Filament\Admin\Resources\Exams\Schemas;

use App\Models\AcademicYear;
use App\Models\School;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;

class ExamForm
{
 public static function configure(Schema $schema): Schema
 {
 return $schema
 ->components([
 Select::make('academic_year_id')
 ->label('Academic Year')
 ->relationship('academicYear', 'name')
 ->required()
 ->searchable()
 ->preload(),

 Select::make('school_id')
 ->label('School')
 ->relationship('school', 'name')
 ->required()
 ->searchable()
 ->preload(),

 TextInput::make('name')
 ->required()
 ->maxLength(255)
 ->placeholder('e.g., Mid-term Examination'),

 Textarea::make('description')
 ->rows(3)
 ->placeholder('Brief description of the examination'),

 Select::make('type')
 ->required()
 ->options([
 'midterm' => 'Mid-term',
 'final' => 'Final',
 'unit_test' => 'Unit Test',
 'quarterly' => 'Quarterly',
 'half_yearly' => 'Half Yearly',
 'annual' => 'Annual',
 'mock' => 'Mock Test',
 'entrance' => 'Entrance Test',
 ]),

 Select::make('status')
 ->required()
 ->options([
 'draft' => 'Draft',
 'scheduled' => 'Scheduled',
 'ongoing' => 'Ongoing',
 'completed' => 'Completed',
 'cancelled' => 'Cancelled',
 ])
 ->default('draft'),

 DatePicker::make('start_date')
 ->required()
 ->native(false),

 DatePicker::make('end_date')
 ->required()
 ->native(false),

 TextInput::make('total_marks')
 ->numeric()
 ->required()
 ->default(100)
 ->suffix('marks'),

 TextInput::make('passing_marks')
 ->numeric()
 ->required()
 ->default(40)
 ->suffix('marks'),

 Textarea::make('instructions')
 ->rows(4)
 ->placeholder('Special instructions for students and teachers'),

 Toggle::make('is_published')
 ->label('Published')
 ->helperText('Students and faculty can see this exam'),
 ]);
 }
}
