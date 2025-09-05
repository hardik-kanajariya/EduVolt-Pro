<?php

namespace App\Filament\Admin\Resources\Subjects\RelationManagers;

use App\Filament\Admin\Resources\Teachers\TeacherResource;
use Filament\Tables\Actions\CreateAction;
use Filament\Actions\AttachAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class TeachersRelationManager extends RelationManager
{
    protected static string $relationship = 'teachers';

    protected static ?string $relatedResource = TeacherResource::class;

    protected static ?string $recordTitleAttribute = 'user.name';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                ImageColumn::make('user.avatar')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl('/images/default-avatar.png'),

                TextColumn::make('user.name')
                    ->label('Teacher Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('employee_id')
                    ->label('Employee ID')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('specialization')
                    ->label('Specialization')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('department')
                    ->label('Department')
                    ->searchable()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('experience_years')
                    ->label('Experience')
                    ->suffix(' years')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'on_leave' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add New Teacher'),
                AttachAction::make()
                    ->label('Assign Existing Teacher')
                    ->preloadRecordSelect(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DetachAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No teachers assigned')
            ->emptyStateDescription('Assign teachers to this subject to get started.')
            ->emptyStateIcon('heroicon-o-users');
    }
}
