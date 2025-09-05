<?php

namespace App\Filament\Admin\Resources\AcademicReportResource\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AcademicReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('report_type')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->color(fn(string $state): string => match ($state) {
                        'student_progress' => 'primary',
                        'class_performance' => 'success',
                        'attendance_summary' => 'warning',
                        'exam_results' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'secondary',
                        'generating' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('format')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('academicYear.name')
                    ->label('Academic Year')
                    ->sortable(),

                TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->sortable(),

                IconColumn::make('is_scheduled')
                    ->boolean()
                    ->label('Scheduled'),

                TextColumn::make('generated_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('file_size')
                    ->formatStateUsing(
                        fn(?string $state): string =>
                        $state ? number_format($state / 1024, 2) . ' KB' : 'N/A'
                    ),
            ])
            ->filters([
                SelectFilter::make('report_type')
                    ->options([
                        'student_progress' => 'Student Progress',
                        'class_performance' => 'Class Performance',
                        'attendance_summary' => 'Attendance Summary',
                        'grade_analysis' => 'Grade Analysis',
                        'subject_performance' => 'Subject Performance',
                        'individual_student' => 'Individual Student',
                        'teacher_evaluation' => 'Teacher Evaluation',
                        'exam_results' => 'Exam Results',
                        'assignment_tracking' => 'Assignment Tracking',
                        'comprehensive' => 'Comprehensive',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'generating' => 'Generating',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),

                SelectFilter::make('format')
                    ->options([
                        'pdf' => 'PDF',
                        'excel' => 'Excel',
                        'csv' => 'CSV',
                        'html' => 'HTML',
                        'json' => 'JSON',
                    ]),

                Filter::make('scheduled_reports')
                    ->query(fn(Builder $query): Builder => $query->where('is_scheduled', true))
                    ->label('Scheduled Reports'),
            ]);
    }
}
