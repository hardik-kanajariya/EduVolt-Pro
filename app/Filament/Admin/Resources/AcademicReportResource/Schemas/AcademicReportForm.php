<?php

namespace App\Filament\Admin\Resources\AcademicReportResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicReportForm
{
 public static function configure(Schema $schema): Schema
 {
 return $schema
 ->components([
 TextInput::make('title')
 ->required()
 ->maxLength(255)
 ->columnSpan(2),

 Textarea::make('description')
 ->rows(3)
 ->columnSpan(2),

 Select::make('report_type')
 ->required()
 ->options([
 'student_progress' => 'Student Progress Report',
 'class_performance' => 'Class Performance Report',
 'attendance_summary' => 'Attendance Summary',
 'grade_analysis' => 'Grade Analysis',
 'subject_performance' => 'Subject Performance',
 'individual_student' => 'Individual Student Report',
 'teacher_evaluation' => 'Teacher Evaluation',
 'exam_results' => 'Examination Results',
 'assignment_tracking' => 'Assignment Tracking',
 'comprehensive' => 'Comprehensive Report',
 ])
 ->reactive()
 ->columnSpan(1),

 Select::make('format')
 ->required()
 ->options([
 'pdf' => 'PDF',
 'excel' => 'Excel',
 'csv' => 'CSV',
 'html' => 'HTML',
 'json' => 'JSON',
 ])
 ->default('pdf')
 ->columnSpan(1),

 Select::make('academic_year_id')
 ->label('Academic Year')
 ->relationship('academicYear', 'name')
 ->required()
 ->searchable()
 ->preload()
 ->columnSpan(1),

 Select::make('school_class_id')
 ->label('Class')
 ->relationship('schoolClass', 'name')
 ->searchable()
 ->preload()
 ->columnSpan(1),

 Select::make('subject_id')
 ->label('Subject')
 ->relationship('subject', 'name')
 ->searchable()
 ->preload()
 ->columnSpan(1),

 Select::make('student_id')
 ->label('Student')
 ->relationship('student', 'name')
 ->searchable()
 ->preload()
 ->columnSpan(1),

 DatePicker::make('date_from')
 ->label('From Date')
 ->columnSpan(1),

 DatePicker::make('date_to')
 ->label('To Date')
 ->columnSpan(1),

 Toggle::make('is_scheduled')
 ->label('Schedule Report Generation')
 ->reactive()
 ->columnSpan(1),

 Select::make('schedule_frequency')
 ->label('Frequency')
 ->options([
 'daily' => 'Daily',
 'weekly' => 'Weekly',
 'monthly' => 'Monthly',
 'quarterly' => 'Quarterly',
 'annually' => 'Annually',
 ])
 ->visible(fn($get) => $get('is_scheduled'))
 ->columnSpan(1),

 DateTimePicker::make('scheduled_at')
 ->label('Next Generation Date')
 ->visible(fn($get) => $get('is_scheduled'))
 ->columnSpan(1),

 Textarea::make('recipients')
 ->label('Email Recipients (one per line)')
 ->rows(3)
 ->helperText('Enter email addresses, one per line')
 ->columnSpan(2),
 ])
 ->columns(2);
 }
}
