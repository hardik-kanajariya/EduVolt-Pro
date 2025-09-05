<?php

namespace App\Filament\Admin\Resources\Teachers\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\SchoolClass;

class AssignedClassesRelationManager extends RelationManager
{
    protected static string $relationship = 'assignedClasses';

    protected static ?string $title = 'Assigned Classes';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('e.g., Grade 5A, Class 10-Science'),

                Forms\Components\Select::make('grade_level')
                    ->options([
                        '1' => 'Grade 1',
                        '2' => 'Grade 2',
                        '3' => 'Grade 3',
                        '4' => 'Grade 4',
                        '5' => 'Grade 5',
                        '6' => 'Grade 6',
                        '7' => 'Grade 7',
                        '8' => 'Grade 8',
                        '9' => 'Grade 9',
                        '10' => 'Grade 10',
                        '11' => 'Grade 11',
                        '12' => 'Grade 12',
                    ])
                    ->required()
                    ->searchable(),

                Forms\Components\Select::make('section')
                    ->options([
                        'A' => 'Section A',
                        'B' => 'Section B',
                        'C' => 'Section C',
                        'D' => 'Section D',
                        'E' => 'Section E',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('room_number')
                    ->maxLength(20)
                    ->placeholder('e.g., Room 101, Lab A'),

                Forms\Components\TextInput::make('capacity')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->default(30),

                Forms\Components\Select::make('academic_year_id')
                    ->relationship('academicYear', 'year')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Textarea::make('description')
                    ->maxLength(500)
                    ->columnSpanFull(),

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
                Tables\Columns\TextColumn::make('name')
                    ->label('Class Name')
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('grade_level')
                    ->label('Grade')
                    ->colors([
                        'success' => ['1', '2', '3', '4', '5'],
                        'warning' => ['6', '7', '8'],
                        'danger' => ['9', '10', '11', '12'],
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('section')
                    ->colors([
                        'blue' => 'A',
                        'green' => 'B',
                        'purple' => 'C',
                        'orange' => 'D',
                        'pink' => 'E',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('room_number')
                    ->label('Room')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('Students')
                    ->badge()
                    ->color('primary')
                    ->counts('students')
                    ->sortable(),

                Tables\Columns\TextColumn::make('capacity')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('academicYear.year')
                    ->label('Academic Year')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('grade_level')
                    ->options([
                        '1' => 'Grade 1',
                        '2' => 'Grade 2',
                        '3' => 'Grade 3',
                        '4' => 'Grade 4',
                        '5' => 'Grade 5',
                        '6' => 'Grade 6',
                        '7' => 'Grade 7',
                        '8' => 'Grade 8',
                        '9' => 'Grade 9',
                        '10' => 'Grade 10',
                        '11' => 'Grade 11',
                        '12' => 'Grade 12',
                    ]),

                Tables\Filters\SelectFilter::make('section')
                    ->options([
                        'A' => 'Section A',
                        'B' => 'Section B',
                        'C' => 'Section C',
                        'D' => 'Section D',
                        'E' => 'Section E',
                    ]),

                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->relationship('academicYear', 'year')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Classes')
                    ->placeholder('All classes')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                Tables\Filters\Filter::make('low_enrollment')
                    ->query(function (Builder $query): Builder {
                        return $query->withCount('students')
                            ->having('students_count', '<', 20);
                    })
                    ->label('Low Enrollment (<20)'),

                Tables\Filters\Filter::make('overcrowded')
                    ->query(function (Builder $query): Builder {
                        return $query->withCount('students')
                            ->whereColumn('students_count', '>', 'capacity');
                    })
                    ->label('Overcrowded'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['class_teacher_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('grade_level')
            ->emptyStateHeading('No classes assigned')
            ->emptyStateDescription('This teacher is not assigned as a class teacher for any classes yet.')
            ->emptyStateIcon('heroicon-o-user-group');
    }
}
