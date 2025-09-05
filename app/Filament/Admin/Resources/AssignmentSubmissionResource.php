<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssignmentSubmissionResource\Pages;
use App\Models\AssignmentSubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AssignmentSubmissionResource extends Resource
{
    protected static ?string $model = AssignmentSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'Assignment Submissions';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Submission Details')
                    ->schema([
                        Forms\Components\Select::make('assignment_id')
                            ->label('Assignment')
                            ->relationship('assignment', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),

                        Forms\Components\Select::make('student_id')
                            ->label('Student')
                            ->relationship('student', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('submitted_at')
                            ->label('Submitted At')
                            ->disabled(),

                        Forms\Components\RichEditor::make('content')
                            ->label('Submission Content')
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('attachments')
                            ->multiple()
                            ->disabled()
                            ->directory('assignment-submissions')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('marks_obtained')
                            ->label('Marks Obtained')
                            ->numeric()
                            ->minValue(0),

                        Forms\Components\Select::make('status')
                            ->options([
                                'submitted' => 'Submitted',
                                'graded' => 'Graded',
                                'returned' => 'Returned',
                                'resubmitted' => 'Resubmitted',
                            ])
                            ->required(),

                        Forms\Components\RichEditor::make('feedback')
                            ->label('Teacher Feedback')
                            ->columnSpanFull(),

                        Forms\Components\DateTimePicker::make('graded_at')
                            ->label('Graded At'),

                        Forms\Components\Hidden::make('graded_by')
                            ->default(Auth::id() ?? null),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('assignment.title')
                    ->label('Assignment')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.admission_number')
                    ->label('Admission No.')
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.schoolClass.name')
                    ->label('Class')
                    ->sortable(),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('marks_obtained')
                    ->label('Marks')
                    ->sortable()
                    ->formatStateUsing(fn($state, $record) => $state ? $state . '/' . $record->assignment->max_marks : '-'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'submitted',
                        'success' => 'graded',
                        'info' => 'returned',
                        'secondary' => 'resubmitted',
                    ]),

                Tables\Columns\BooleanColumn::make('is_late')
                    ->label('Late')
                    ->getStateUsing(fn($record) => $record->submitted_at > $record->assignment->due_date),

                Tables\Columns\TextColumn::make('graded_at')
                    ->label('Graded At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('gradedBy.name')
                    ->label('Graded By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('assignment_id')
                    ->label('Assignment')
                    ->relationship('assignment', 'title')
                    ->searchable(),

                SelectFilter::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'graded' => 'Graded',
                        'returned' => 'Returned',
                        'resubmitted' => 'Resubmitted',
                    ]),

                Filter::make('class')
                    ->form([
                        Forms\Components\Select::make('class_id')
                            ->label('Class')
                            ->options(\App\Models\SchoolClass::pluck('name', 'id'))
                            ->searchable(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['class_id'],
                            fn(Builder $query, $classId): Builder => $query->whereHas('student', function (Builder $query) use ($classId) {
                                $query->where('class_id', $classId);
                            })
                        );
                    }),

                Filter::make('late_submissions')
                    ->label('Late Submissions')
                    ->query(fn(Builder $query): Builder => $query->whereRaw('submitted_at > CONCAT(DATE(assignments.due_date), " ", TIME(assignments.due_time))')
                        ->join('assignments', 'assignment_submissions.assignment_id', '=', 'assignments.id')),

                Filter::make('pending_grading')
                    ->label('Pending Grading')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'submitted')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('grade')
                    ->icon('heroicon-o-star')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'submitted')
                    ->url(fn($record) => route('filament.admin.resources.assignment-submissions.grade', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('submitted_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignmentSubmissions::route('/'),
            'view' => Pages\ViewAssignmentSubmission::route('/{record}'),
            'edit' => Pages\EditAssignmentSubmission::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['assignment', 'student', 'student.schoolClass', 'gradedBy']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'submitted')->count();
    }
}
