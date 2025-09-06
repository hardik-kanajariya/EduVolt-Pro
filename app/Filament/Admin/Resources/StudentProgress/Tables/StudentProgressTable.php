<?php

namespace App\Filament\Admin\Resources\StudentProgress\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentProgressTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.id')
                    ->searchable(),
                TextColumn::make('academicYear.name')
                    ->searchable(),
                TextColumn::make('subject.name')
                    ->searchable(),
                TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('term')
                    ->searchable(),
                TextColumn::make('academic_year'),
                TextColumn::make('attendance_percentage')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('assignment_average')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('exam_average')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('overall_grade')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('letter_grade')
                    ->searchable(),
                TextColumn::make('gpa')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_assignments')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('submitted_assignments')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('late_submissions')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_exams')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('exams_taken')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('exams_passed')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_classes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('classes_attended')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('classes_absent')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('classes_late')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('performance_trend'),
                TextColumn::make('previous_grade')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('grade_change')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('behavioral_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('effort_level'),
                TextColumn::make('participation_level'),
                TextColumn::make('last_updated_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('conduct'),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reporting_period_start')
                    ->date()
                    ->sortable(),
                TextColumn::make('reporting_period_end')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
