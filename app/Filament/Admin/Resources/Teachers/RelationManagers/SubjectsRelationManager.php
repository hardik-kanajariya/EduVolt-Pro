<?php

namespace App\Filament\Admin\Resources\Teachers\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Subject;

class SubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'subjects';

    protected static ?string $title = 'Teaching Subjects';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('description')
                    ->maxLength(500)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('credits')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10)
                    ->default(3),

                Forms\Components\Select::make('department')
                    ->options([
                        'science' => 'Science',
                        'mathematics' => 'Mathematics',
                        'english' => 'English',
                        'social_studies' => 'Social Studies',
                        'arts' => 'Arts',
                        'physical_education' => 'Physical Education',
                        'computer_science' => 'Computer Science',
                        'languages' => 'Languages',
                        'other' => 'Other',
                    ])
                    ->required(),

                Forms\Components\Select::make('grade_level')
                    ->options([
                        'primary' => 'Primary (1-5)',
                        'middle' => 'Middle (6-8)',
                        'high' => 'High (9-12)',
                        'all' => 'All Levels',
                    ])
                    ->required()
                    ->default('all'),

                Forms\Components\Toggle::make('is_elective')
                    ->label('Elective Subject')
                    ->default(false),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Subject Code')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Subject Name')
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('department')
                    ->colors([
                        'blue' => 'science',
                        'green' => 'mathematics',
                        'purple' => 'english',
                        'orange' => 'social_studies',
                        'pink' => 'arts',
                        'yellow' => 'physical_education',
                        'indigo' => 'computer_science',
                        'red' => 'languages',
                        'gray' => 'other',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('grade_level')
                    ->label('Grade Level')
                    ->colors([
                        'success' => 'primary',
                        'warning' => 'middle',
                        'danger' => 'high',
                        'info' => 'all',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('credits')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_elective')
                    ->label('Elective')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('Students')
                    ->badge()
                    ->color('info')
                    ->counts('students')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->options([
                        'science' => 'Science',
                        'mathematics' => 'Mathematics',
                        'english' => 'English',
                        'social_studies' => 'Social Studies',
                        'arts' => 'Arts',
                        'physical_education' => 'Physical Education',
                        'computer_science' => 'Computer Science',
                        'languages' => 'Languages',
                        'other' => 'Other',
                    ]),

                Tables\Filters\SelectFilter::make('grade_level')
                    ->options([
                        'primary' => 'Primary (1-5)',
                        'middle' => 'Middle (6-8)',
                        'high' => 'High (9-12)',
                        'all' => 'All Levels',
                    ]),

                Tables\Filters\TernaryFilter::make('is_elective')
                    ->label('Elective Subjects')
                    ->placeholder('All subjects')
                    ->trueLabel('Elective only')
                    ->falseLabel('Core subjects only'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Subjects')
                    ->placeholder('All subjects')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\DatePicker::make('assigned_date')
                            ->default(now())
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(500),
                    ]),
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No subjects assigned')
            ->emptyStateDescription('Assign subjects to this teacher to get started.')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }
}
