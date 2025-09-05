<?php

namespace App\Filament\Admin\Resources\Students\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProgressRelationManager extends RelationManager
{
    protected static string $relationship = 'progress';

    protected static ?string $title = 'Academic Progress';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('term')
                    ->options([
                        'first' => 'First Term',
                        'second' => 'Second Term',
                        'third' => 'Third Term',
                        'annual' => 'Annual',
                    ])
                    ->required()
                    ->default('first'),

                Forms\Components\TextInput::make('assessment_score')
                    ->label('Assessment Score')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%')
                    ->required(),

                Forms\Components\TextInput::make('assignment_score')
                    ->label('Assignment Score')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%'),

                Forms\Components\TextInput::make('participation_score')
                    ->label('Participation Score')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%'),

                Forms\Components\TextInput::make('overall_grade')
                    ->label('Overall Grade')
                    ->maxLength(5)
                    ->placeholder('A+, A, B+, etc.'),

                Forms\Components\Select::make('status')
                    ->options([
                        'excellent' => 'Excellent',
                        'good' => 'Good',
                        'satisfactory' => 'Satisfactory',
                        'needs_improvement' => 'Needs Improvement',
                        'poor' => 'Poor',
                    ])
                    ->required()
                    ->default('satisfactory'),

                Forms\Components\Textarea::make('teacher_comments')
                    ->label('Teacher Comments')
                    ->maxLength(1000)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('areas_of_strength')
                    ->label('Areas of Strength')
                    ->maxLength(500)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('areas_for_improvement')
                    ->label('Areas for Improvement')
                    ->maxLength(500)
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('recorded_date')
                    ->label('Recorded Date')
                    ->required()
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subject.name')
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('term')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'first' => 'primary',
                        'second' => 'success',
                        'third' => 'warning',
                        'annual' => 'info',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('assessment_score')
                    ->label('Assessment')
                    ->formatStateUsing(fn($state) => $state ? $state . '%' : 'N/A')
                    ->badge()
                    ->color(function ($state) {
                        if (!$state) return 'gray';
                        if ($state >= 90) return 'success';
                        if ($state >= 70) return 'primary';
                        if ($state >= 50) return 'warning';
                        return 'danger';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignment_score')
                    ->label('Assignments')
                    ->formatStateUsing(fn($state) => $state ? $state . '%' : 'N/A')
                    ->badge()
                    ->color(function ($state) {
                        if (!$state) return 'gray';
                        if ($state >= 90) return 'success';
                        if ($state >= 70) return 'primary';
                        if ($state >= 50) return 'warning';
                        return 'danger';
                    })
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('participation_score')
                    ->label('Participation')
                    ->formatStateUsing(fn($state) => $state ? $state . '%' : 'N/A')
                    ->badge()
                    ->color(function ($state) {
                        if (!$state) return 'gray';
                        if ($state >= 90) return 'success';
                        if ($state >= 70) return 'primary';
                        if ($state >= 50) return 'warning';
                        return 'danger';
                    })
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('overall_grade')
                    ->label('Grade')
                    ->badge()
                    ->color(function ($state) {
                        if (str_contains($state, 'A')) return 'success';
                        if (str_contains($state, 'B')) return 'primary';
                        if (str_contains($state, 'C')) return 'warning';
                        return 'danger';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'excellent' => 'success',
                        'good' => 'primary',
                        'satisfactory' => 'info',
                        'needs_improvement' => 'warning',
                        'poor' => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('teacher_comments')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('recorded_date')
                    ->label('Recorded')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('term')
                    ->options([
                        'first' => 'First Term',
                        'second' => 'Second Term',
                        'third' => 'Third Term',
                        'annual' => 'Annual',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'excellent' => 'Excellent',
                        'good' => 'Good',
                        'satisfactory' => 'Satisfactory',
                        'needs_improvement' => 'Needs Improvement',
                        'poor' => 'Poor',
                    ]),

                Tables\Filters\SelectFilter::make('subject_id')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('high_performers')
                    ->query(fn(Builder $query): Builder => $query->where('assessment_score', '>=', 80))
                    ->label('High Performers (80%+)'),

                Tables\Filters\Filter::make('needs_attention')
                    ->query(fn(Builder $query): Builder => $query->where('assessment_score', '<', 60))
                    ->label('Needs Attention (<60%)'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['student_id'] = $this->ownerRecord->id;
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
            ->defaultSort('recorded_date', 'desc')
            ->emptyStateHeading('No progress records found')
            ->emptyStateDescription('Start tracking this student\'s academic progress.')
            ->emptyStateIcon('heroicon-o-chart-bar');
    }
}
