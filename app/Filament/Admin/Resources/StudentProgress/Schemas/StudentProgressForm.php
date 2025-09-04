<?php

namespace App\Filament\Admin\Resources\StudentProgress\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
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
 Select::make('academic_year_id')
 ->relationship('academicYear', 'name')
 ->required(),
 Select::make('subject_id')
 ->relationship('subject', 'name')
 ->required(),
 TextInput::make('class_id')
 ->required()
 ->numeric(),
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
 TextInput::make('gpa')
 ->numeric(),
 TextInput::make('total_assignments')
 ->required()
 ->numeric()
 ->default(0),
 TextInput::make('submitted_assignments')
 ->required()
 ->numeric()
 ->default(0),
 TextInput::make('late_submissions')
 ->required()
 ->numeric()
 ->default(0),
 TextInput::make('total_exams')
 ->required()
 ->numeric()
 ->default(0),
 TextInput::make('exams_taken')
 ->required()
 ->numeric()
 ->default(0),
 TextInput::make('exams_passed')
 ->required()
 ->numeric()
 ->default(0),
 TextInput::make('total_classes')
 ->required()
 ->numeric()
 ->default(0),
 TextInput::make('classes_attended')
 ->required()
 ->numeric()
 ->default(0),
 TextInput::make('classes_absent')
 ->required()
 ->numeric()
 ->default(0),
 TextInput::make('classes_late')
 ->required()
 ->numeric()
 ->default(0),
 Select::make('performance_trend')
 ->options([
 'improving' => 'Improving',
 'declining' => 'Declining',
 'stable' => 'Stable',
 'excellent' => 'Excellent',
 'needs_attention' => 'Needs attention',
 ]),
 TextInput::make('previous_grade')
 ->numeric(),
 TextInput::make('grade_change')
 ->numeric(),
 TextInput::make('behavioral_score')
 ->numeric(),
 TextInput::make('achievements'),
 TextInput::make('areas_of_concern'),
 Textarea::make('teacher_comments')
 ->columnSpanFull(),
 Select::make('effort_level')
 ->options([
 'excellent' => 'Excellent',
 'good' => 'Good',
 'satisfactory' => 'Satisfactory',
 'needs_improvement' => 'Needs improvement',
 'poor' => 'Poor',
 ]),
 Select::make('participation_level')
 ->options([
 'excellent' => 'Excellent',
 'good' => 'Good',
 'satisfactory' => 'Satisfactory',
 'needs_improvement' => 'Needs improvement',
 'poor' => 'Poor',
 ]),
 DateTimePicker::make('last_updated_at'),
 Select::make('conduct')
 ->options([
 'excellent' => 'Excellent',
 'good' => 'Good',
 'satisfactory' => 'Satisfactory',
 'needs_improvement' => 'Needs improvement',
 ])
 ->default('good')
 ->required(),
 TextInput::make('updated_by')
 ->numeric(),
 DatePicker::make('reporting_period_start'),
 DatePicker::make('reporting_period_end'),
 ]);
 }
}
