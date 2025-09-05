<?php

namespace App\Filament\Admin\Resources\Students\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;

class AssignmentSubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignmentSubmissions';

    protected static ?string $title = 'Assignment Submissions';

    protected static ?string $icon = 'heroicon-o-document-text';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('assignment_id')
                    ->relationship('assignment', 'title')
                    ->required(),

                Forms\Components\DateTimePicker::make('submitted_at')
                    ->label('Submission Date')
                    ->default(now()),

                Forms\Components\Select::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'late' => 'Late',
                        'pending_review' => 'Pending Review',
                        'needs_revision' => 'Needs Revision',
                        'approved' => 'Approved',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('grade')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),

                Forms\Components\RichEditor::make('feedback')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('assignment.title')
            ->columns([
                Tables\Columns\TextColumn::make('assignment.title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'submitted' => 'primary',
                        'warning' => 'late',
                        'info' => 'pending_review',
                        'danger' => 'needs_revision',
                        'success' => 'approved',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('grade')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
