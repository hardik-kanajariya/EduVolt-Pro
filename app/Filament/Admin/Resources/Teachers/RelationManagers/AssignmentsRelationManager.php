<?php

namespace App\Filament\Admin\Resources\Teachers\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Assignment;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    protected static ?string $title = 'Created Assignments';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Chapter 5 Quiz, Mathematics Project'),

                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(2000)
                    ->columnSpanFull()
                    ->placeholder('Detailed description of the assignment requirements...'),

                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('class_id')
                    ->relationship('schoolClass', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\DateTimePicker::make('due_date')
                    ->required()
                    ->minDate(now())
                    ->default(now()->addDays(7)),

                Forms\Components\TextInput::make('max_score')
                    ->label('Maximum Score')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(1000)
                    ->default(100)
                    ->suffix('points'),

                Forms\Components\Select::make('type')
                    ->options([
                        'homework' => 'Homework',
                        'project' => 'Project',
                        'quiz' => 'Quiz',
                        'exam' => 'Exam',
                        'presentation' => 'Presentation',
                        'lab' => 'Lab Work',
                        'essay' => 'Essay',
                        'other' => 'Other',
                    ])
                    ->required()
                    ->default('homework'),

                Forms\Components\Select::make('difficulty_level')
                    ->options([
                        'easy' => 'Easy',
                        'medium' => 'Medium',
                        'hard' => 'Hard',
                        'advanced' => 'Advanced',
                    ])
                    ->required()
                    ->default('medium'),

                Forms\Components\FileUpload::make('attachment')
                    ->label('Assignment File')
                    ->directory('assignments')
                    ->acceptedFileTypes(['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'])
                    ->maxSize(10240) // 10MB
                    ->downloadable()
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('instructions')
                    ->label('Special Instructions')
                    ->maxLength(1000)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('allow_late_submission')
                    ->label('Allow Late Submissions')
                    ->default(false),

                Forms\Components\TextInput::make('late_penalty')
                    ->label('Late Penalty (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(10)
                    ->suffix('%')
                    ->visible(fn(Forms\Get $get): bool => $get('allow_late_submission')),

                Forms\Components\Toggle::make('is_published')
                    ->label('Publish Assignment')
                    ->default(true)
                    ->helperText('Students can only see published assignments'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable()
                    ->weight('medium')
                    ->limit(40),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('type')
                    ->colors([
                        'blue' => 'homework',
                        'green' => 'project',
                        'yellow' => 'quiz',
                        'red' => 'exam',
                        'purple' => 'presentation',
                        'orange' => 'lab',
                        'indigo' => 'essay',
                        'gray' => 'other',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('difficulty_level')
                    ->label('Difficulty')
                    ->colors([
                        'success' => 'easy',
                        'warning' => 'medium',
                        'danger' => 'hard',
                        'purple' => 'advanced',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->dateTime()
                    ->sortable()
                    ->badge()
                    ->color(function ($record) {
                        if ($record->due_date < now()) {
                            return 'danger'; // Past due
                        } elseif ($record->due_date < now()->addDays(2)) {
                            return 'warning'; // Due soon
                        }
                        return 'success';
                    }),

                Tables\Columns\TextColumn::make('max_score')
                    ->label('Max Score')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->badge()
                    ->color('info')
                    ->counts('submissions')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('allow_late_submission')
                    ->label('Late Allowed')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'homework' => 'Homework',
                        'project' => 'Project',
                        'quiz' => 'Quiz',
                        'exam' => 'Exam',
                        'presentation' => 'Presentation',
                        'lab' => 'Lab Work',
                        'essay' => 'Essay',
                        'other' => 'Other',
                    ]),

                Tables\Filters\SelectFilter::make('difficulty_level')
                    ->options([
                        'easy' => 'Easy',
                        'medium' => 'Medium',
                        'hard' => 'Hard',
                        'advanced' => 'Advanced',
                    ]),

                Tables\Filters\SelectFilter::make('subject_id')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('class_id')
                    ->relationship('schoolClass', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published Status')
                    ->placeholder('All assignments')
                    ->trueLabel('Published only')
                    ->falseLabel('Unpublished only'),

                Tables\Filters\Filter::make('due_soon')
                    ->query(fn(Builder $query): Builder => $query->whereBetween('due_date', [now(), now()->addDays(7)]))
                    ->label('Due This Week'),

                Tables\Filters\Filter::make('overdue')
                    ->query(fn(Builder $query): Builder => $query->where('due_date', '<', now()))
                    ->label('Overdue'),

                Tables\Filters\Filter::make('no_submissions')
                    ->query(fn(Builder $query): Builder => $query->whereDoesntHave('submissions'))
                    ->label('No Submissions Yet'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['teacher_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (Assignment $record): void {
                        $newAssignment = $record->replicate();
                        $newAssignment->title = $record->title . ' (Copy)';
                        $newAssignment->due_date = now()->addDays(7);
                        $newAssignment->save();
                    })
                    ->requiresConfirmation()
                    ->color('info'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->action(fn($records) => $records->each->update(['is_published' => true]))
                        ->icon('heroicon-o-eye')
                        ->color('success'),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->action(fn($records) => $records->each->update(['is_published' => false]))
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning'),
                ]),
            ])
            ->defaultSort('due_date', 'asc')
            ->emptyStateHeading('No assignments created')
            ->emptyStateDescription('Create your first assignment for students.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
