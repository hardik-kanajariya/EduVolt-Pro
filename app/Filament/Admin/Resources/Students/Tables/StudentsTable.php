<?php

namespace App\Filament\Admin\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('admission_number')
                    ->label('Admission No.')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('user.name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->sortable(),
                    
                TextColumn::make('roll_number')
                    ->label('Roll No.')
                    ->searchable(),
                    
                TextColumn::make('parent_name')
                    ->label('Parent/Guardian')
                    ->searchable(),
                    
                TextColumn::make('parent_phone')
                    ->label('Parent Phone')
                    ->searchable(),
                    
                TextColumn::make('admission_date')
                    ->label('Admission Date')
                    ->date()
                    ->sortable(),
                    
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'transferred' => 'warning',
                        'graduated' => 'info',
                        default => 'gray',
                    }),
                    
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'transferred' => 'Transferred',
                        'graduated' => 'Graduated',
                    ]),
                SelectFilter::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name'),
                SelectFilter::make('class_id')
                    ->label('Class')
                    ->relationship('schoolClass', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('admission_number');
    }
}
