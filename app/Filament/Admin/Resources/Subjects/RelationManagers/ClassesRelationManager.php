<?php

namespace App\Filament\Admin\Resources\Subjects\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClassesRelationManager extends RelationManager
{
    protected static string $relationship = 'classes';

    public function form(Form $form): Form
    {
        return $form
            ->components([
                TextInput::make('name')
                    ->label('Class Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('section')
                    ->label('Section')
                    ->required()
                    ->maxLength(10),

                Select::make('grade_level')
                    ->label('Grade Level')
                    ->options([
                        'kindergarten' => 'Kindergarten',
                        'grade_1' => 'Grade 1',
                        'grade_2' => 'Grade 2',
                        'grade_3' => 'Grade 3',
                        'grade_4' => 'Grade 4',
                        'grade_5' => 'Grade 5',
                        'grade_6' => 'Grade 6',
                        'grade_7' => 'Grade 7',
                        'grade_8' => 'Grade 8',
                        'grade_9' => 'Grade 9',
                        'grade_10' => 'Grade 10',
                        'grade_11' => 'Grade 11',
                        'grade_12' => 'Grade 12',
                    ])
                    ->required(),

                Select::make('academic_year_id')
                    ->label('Academic Year')
                    ->relationship('academicYear', 'year')
                    ->required()
                    ->searchable(),

                TextInput::make('capacity')
                    ->label('Maximum Capacity')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100),

                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Class Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->section ? "Section: {$record->section}" : null),

                TextColumn::make('section')
                    ->label('Section')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('grade_level')
                    ->label('Grade Level')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'kindergarten' => 'KG',
                        'grade_1' => 'Grade 1',
                        'grade_2' => 'Grade 2',
                        'grade_3' => 'Grade 3',
                        'grade_4' => 'Grade 4',
                        'grade_5' => 'Grade 5',
                        'grade_6' => 'Grade 6',
                        'grade_7' => 'Grade 7',
                        'grade_8' => 'Grade 8',
                        'grade_9' => 'Grade 9',
                        'grade_10' => 'Grade 10',
                        'grade_11' => 'Grade 11',
                        'grade_12' => 'Grade 12',
                        default => $state,
                    }),

                TextColumn::make('academicYear.year')
                    ->label('Academic Year')
                    ->sortable(),

                TextColumn::make('capacity')
                    ->label('Capacity')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('students_count')
                    ->label('Students')
                    ->counts('students')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add New Class'),
                AttachAction::make()
                    ->label('Assign Existing Class')
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
