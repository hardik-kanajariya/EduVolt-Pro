<?php

namespace App\Filament\Admin\Resources\Students\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GradesRelationManager extends RelationManager
{
 protected static string $relationship = 'grades';

 protected static ?string $title = 'Academic Grades';

 public function form(Schema $schema): Schema
 {
 return $schema
 ->schema([
 Select::make('subject_id')
 ->label('Subject')
 ->relationship('subject', 'name')
 ->searchable()
 ->preload()
 ->required()
 ->helperText(' Select the subject for this grade')
 ->columnSpan(1),

 Select::make('exam_id')
 ->label('Exam/Assessment')
 ->relationship('exam', 'name')
 ->searchable()
 ->preload()
 ->required()
 ->helperText(' Select the exam or assessment')
 ->columnSpan(1),

 TextInput::make('grade')
 ->label('Grade')
 ->required()
 ->maxLength(5)
 ->placeholder('A+, B, 85, etc.')
 ->helperText(' Enter the grade (letter or numeric)')
 ->columnSpan(1),

 TextInput::make('marks_obtained')
 ->label('Marks Obtained')
 ->numeric()
 ->minValue(0)
 ->placeholder('75')
 ->helperText(' Marks achieved by student')
 ->columnSpan(1),

 TextInput::make('total_marks')
 ->label('Total Marks')
 ->numeric()
 ->minValue(1)
 ->placeholder('100')
 ->helperText(' Maximum marks for this assessment')
 ->columnSpan(1),

 TextInput::make('percentage')
 ->label('Percentage')
 ->numeric()
 ->minValue(0)
 ->maxValue(100)
 ->suffix('%')
 ->placeholder('75.5')
 ->helperText(' Percentage score')
 ->columnSpan(1),

 DatePicker::make('graded_at')
 ->label('Grade Date')
 ->required()
 ->native(false)
 ->format('Y-m-d')
 ->displayFormat('M j, Y')
 ->helperText(' Date when grade was assigned')
 ->default(now())
 ->columnSpan(1),

 Select::make('grade_type')
 ->label('Grade Type')
 ->options([
 'assignment' => ' Assignment',
 'quiz' => ' Quiz',
 'midterm' => ' Midterm Exam',
 'final' => ' Final Exam',
 'project' => ' Project',
 'participation' => ' Participation',
 'homework' => ' Homework',
 ])
 ->required()
 ->helperText(' Type of assessment')
 ->columnSpan(1),

 Textarea::make('comments')
 ->label('Teacher Comments')
 ->rows(3)
 ->placeholder('Excellent work, needs improvement, etc.')
 ->helperText(' Optional feedback from teacher')
 ->columnSpanFull(),
 ])
 ->columns(3);
 }

 public function table(Table $table): Table
 {
 return $table
 ->recordTitleAttribute('subject.name')
 ->columns([
 TextColumn::make('subject.name')
 ->label('Subject')
 ->sortable()
 ->searchable()
 ->weight('medium')
 ->icon('heroicon-o-book-open')
 ->iconColor('info'),

 TextColumn::make('exam.name')
 ->label('Assessment')
 ->sortable()
 ->searchable()
 ->description(fn($record): ?string => $record->grade_type ? ucfirst($record->grade_type) : null)
 ->icon('heroicon-o-document-text')
 ->iconColor('primary'),

 TextColumn::make('grade')
 ->label('Grade')
 ->sortable()
 ->weight('bold')
 ->alignCenter()
 ->badge()
 ->color(function ($state): string {
 // Determine color based on grade
 if (is_numeric($state)) {
 $numeric = (float)$state;
 if ($numeric >= 90) return 'success';
 if ($numeric >= 80) return 'info';
 if ($numeric >= 70) return 'warning';
 return 'danger';
 }

 return match (strtoupper($state)) {
 'A+', 'A' => 'success',
 'B+', 'B' => 'info',
 'C+', 'C' => 'warning',
 'D+', 'D', 'F' => 'danger',
 default => 'gray',
 };
 }),

 TextColumn::make('score_display')
 ->label('Score')
 ->state(function ($record): string {
 if ($record->marks_obtained && $record->total_marks) {
 return "{$record->marks_obtained}/{$record->total_marks}";
 }
 return 'N/A';
 })
 ->alignCenter()
 ->description(fn($record): ?string => $record->percentage ? $record->percentage . '%' : null)
 ->icon('heroicon-o-calculator')
 ->iconColor('success'),

 TextColumn::make('percentage')
 ->label('Percentage')
 ->sortable()
 ->alignCenter()
 ->suffix('%')
 ->badge()
 ->color(function (?float $state): string {
 if (!$state) return 'gray';
 if ($state >= 90) return 'success';
 if ($state >= 80) return 'info';
 if ($state >= 70) return 'warning';
 return 'danger';
 }),

 TextColumn::make('grade_type')
 ->label('Type')
 ->badge()
 ->formatStateUsing(fn(?string $state): string => match ($state) {
 'assignment' => ' Assignment',
 'quiz' => ' Quiz',
 'midterm' => ' Midterm',
 'final' => ' Final',
 'project' => ' Project',
 'participation' => ' Participation',
 'homework' => ' Homework',
 default => ucfirst($state ?? 'N/A'),
 })
 ->color('purple'),

 TextColumn::make('graded_at')
 ->label('Date')
 ->date('M j, Y')
 ->sortable()
 ->description(fn($record): string => $record->graded_at ? \Carbon\Carbon::parse($record->graded_at)->diffForHumans() : '')
 ->icon('heroicon-o-calendar')
 ->iconColor('warning'),

 TextColumn::make('comments')
 ->label('Comments')
 ->limit(50)
 ->tooltip(function (TextColumn $column): ?string {
 $state = $column->getState();
 if (strlen($state) <= 50) {
 return null;
 }
 return $state;
 })
 ->placeholder('No comments')
 ->toggleable(isToggledHiddenByDefault: true),

 TextColumn::make('created_at')
 ->label('Recorded')
 ->dateTime('M j, Y g:i A')
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true)
 ->description(fn($record): string => $record->created_at->diffForHumans()),
 ])
 ->filters([
 SelectFilter::make('subject_id')
 ->label('Subject')
 ->relationship('subject', 'name')
 ->searchable()
 ->preload(),

 SelectFilter::make('grade_type')
 ->label('Assessment Type')
 ->options([
 'assignment' => ' Assignment',
 'quiz' => ' Quiz',
 'midterm' => ' Midterm Exam',
 'final' => ' Final Exam',
 'project' => ' Project',
 'participation' => ' Participation',
 'homework' => ' Homework',
 ])
 ->multiple(),

 Tables\Filters\Filter::make('high_grades')
 ->label('High Grades (A/90%+)')
 ->query(fn(Builder $query): Builder => $query->where(function ($q) {
 $q->where('grade', 'like', 'A%')
 ->orWhere('percentage', '>=', 90);
 }))
 ->toggle(),

 Tables\Filters\Filter::make('needs_improvement')
 ->label('Needs Improvement (C-/70%-)')
 ->query(fn(Builder $query): Builder => $query->where(function ($q) {
 $q->whereIn('grade', ['C-', 'D+', 'D', 'D-', 'F'])
 ->orWhere('percentage', '<=', 70);
 }))
 ->toggle(),
 ])
 ->recordActions([
 EditAction::make(),
 DeleteAction::make(),
 ])
 ->toolbarActions([
 CreateAction::make(),
 ])
 ->defaultSort('graded_at', 'desc')
 ->striped()
 ->emptyStateHeading('No grades recorded')
 ->emptyStateDescription('Start by adding the first grade for this student.')
 ->emptyStateIcon('heroicon-o-star');
 }
}
