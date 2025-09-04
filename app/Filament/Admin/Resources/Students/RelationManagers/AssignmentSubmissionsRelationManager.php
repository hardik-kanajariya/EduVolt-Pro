<?php

namespace App\Filament\Admin\Resources\Students\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\AssignmentSubmission;
use BackedEnum;

class AssignmentSubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignmentSubmissions';

    protected static ?string $title = 'Assignment Submissions';

    protected static string|BackedEnum|null $icon = 'heroicon-o-document-text';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('assignment_id')
                    ->relationship('assignment', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\DateTimePicker::make('submitted_at')
                    ->required()
                    ->default(now())
                    ->maxDate(now()),

                Forms\Components\FileUpload::make('file_path')
                    ->label('Submission File')
                    ->directory('assignment-submissions')
                    ->acceptedFileTypes(['pdf', 'doc', 'docx', 'txt', 'jpg', 'png'])
                    ->maxSize(10240) // 10MB
                    ->downloadable()
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('content')
                    ->label('Text Submission')
                    ->maxLength(5000)
                    ->columnSpanFull()
                    ->helperText('If submitting text instead of a file'),

                Forms\Components\Select::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'graded' => 'Graded',
                        'returned' => 'Returned',
                        'late' => 'Late Submission',
                    ])
                    ->required()
                    ->default('submitted'),

                Forms\Components\TextInput::make('score')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%'),

                Forms\Components\Textarea::make('feedback')
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('assignment.title')
            ->columns([
                Tables\Columns\TextColumn::make('assignment.title')
                    ->label('Assignment')
                    ->sortable()
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('assignment.due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable()
                    ->badge()
                    ->color(function ($record) {
                        if ($record->assignment && $record->submitted_at > $record->assignment->due_date) {
                            return 'warning'; // Late submission
                        }
                        return 'success';
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'submitted' => 'info',
                        'graded' => 'success',
                        'returned' => 'primary',
                        'late' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->formatStateUsing(fn($state) => $state ? $state . '%' : 'Not Graded')
                    ->badge()
                    ->color(function ($state) {
                        if (!$state) return 'gray';
                        if ($state >= 90) return 'success';
                        if ($state >= 70) return 'primary';
                        if ($state >= 50) return 'warning';
                        return 'danger';
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('file_path')
                    ->label('File')
                    ->boolean()
                    ->trueIcon('heroicon-o-document')
                    ->falseIcon('heroicon-o-minus'),

                Tables\Columns\TextColumn::make('feedback')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'graded' => 'Graded',
                        'returned' => 'Returned',
                        'late' => 'Late Submission',
                    ]),

                Tables\Filters\SelectFilter::make('assignment_id')
                    ->relationship('assignment', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('submission_date')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('submitted_at', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('submitted_at', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('graded')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('score'))
                    ->label('Graded Only'),

                Tables\Filters\Filter::make('ungraded')
                    ->query(fn(Builder $query): Builder => $query->whereNull('score'))
                    ->label('Ungraded Only'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['student_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->recordBulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->emptyStateHeading('No assignment submissions found')
            ->emptyStateDescription('This student has not submitted any assignments yet.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
