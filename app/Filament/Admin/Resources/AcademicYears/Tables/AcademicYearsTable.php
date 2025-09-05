<?php

namespace App\Filament\Admin\Resources\AcademicYears\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class AcademicYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school.name')
                    ->label('School')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->school?->address ?? 'No address')
                    ->wrap(),

                TextColumn::make('name')
                    ->label('Academic Year')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable()
                    ->copyMessage('Academic year name copied!')
                    ->description(
                        fn($record) =>
                        $record->start_date && $record->end_date
                            ? $record->start_date->format('M j, Y') . ' - ' . $record->end_date->format('M j, Y')
                            : 'Duration not set'
                    ),

                TextColumn::make('duration')
                    ->label('Duration')
                    ->getStateUsing(function ($record): string {
                        if ($record->start_date && $record->end_date) {
                            $diffInDays = $record->start_date->diffInDays($record->end_date) + 1;
                            $diffInMonths = $record->start_date->diffInMonths($record->end_date);
                            return "{$diffInDays} days ({$diffInMonths} months)";
                        }
                        return 'Not set';
                    })
                    ->badge()
                    ->color(fn($state) => $state === 'Not set' ? 'danger' : 'info')
                    ->sortable(false),

                TextColumn::make('progress')
                    ->label('Progress')
                    ->getStateUsing(function ($record): string {
                        if ($record->start_date && $record->end_date) {
                            $total = $record->start_date->diffInDays($record->end_date);
                            $elapsed = $total > 0 ? min($total, max(0, now()->diffInDays($record->start_date))) : 0;
                            $percentage = $total > 0 ? round(($elapsed / $total) * 100, 1) : 0;
                            return "{$percentage}%";
                        }
                        return '0%';
                    })
                    ->badge()
                    ->color(function ($state) {
                        $percentage = (float) str_replace('%', '', $state);
                        return match (true) {
                            $percentage >= 90 => 'danger',
                            $percentage >= 75 => 'warning',
                            $percentage >= 25 => 'success',
                            default => 'gray',
                        };
                    }),

                TextColumn::make('is_current')
                    ->label('Current')
                    ->formatStateUsing(fn($state) => $state ? 'Current Year' : 'Not Current')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'gray')
                    ->icon(fn(bool $state): string => $state ? 'heroicon-o-star' : 'heroicon-o-minus'),

                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'active' => ' Active',
                        'inactive' => ' Inactive',
                        'draft' => ' Draft',
                        'completed' => ' Completed',
                        default => ' ' . ucfirst($state)
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'draft' => 'warning',
                        'completed' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('classes_count')
                    ->label('Classes')
                    ->counts('classes')
                    ->badge()
                    ->color('primary')
                    ->description('Total classes'),

                TextColumn::make('active_classes_count')
                    ->label('Active Classes')
                    ->getStateUsing(fn($record) => $record->classes()->where('status', 'active')->count())
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => ' Active',
                        'inactive' => ' Inactive',
                        'draft' => ' Draft',
                        'completed' => ' Completed',
                    ])
                    ->multiple(),

                SelectFilter::make('is_current')
                    ->label('Current Year')
                    ->options([
                        1 => 'Current Academic Year',
                        0 => 'Not Current',
                    ]),

                SelectFilter::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('year_range')
                    ->label('Year Range')
                    ->options([
                        '2020-2025' => '2020-2025',
                        '2025-2030' => '2025-2030',
                        '2030-2035' => '2030-2035',
                    ])
                    ->query(function ($query, array $data) {
                        if (!empty($data['value'])) {
                            [$startYear, $endYear] = explode('-', $data['value']);
                            $query->where(function ($q) use ($startYear, $endYear) {
                                $q->whereBetween('start_date', ["{$startYear}-01-01", "{$endYear}-12-31"])
                                    ->orWhereBetween('end_date', ["{$startYear}-01-01", "{$endYear}-12-31"]);
                            });
                        }
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),
                EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon('heroicon-o-trash'),

                    BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'active']);
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Activate Academic Years')
                        ->modalDescription('Are you sure you want to activate the selected academic years?'),

                    BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'status' => 'inactive',
                                    'is_current' => false,
                                ]);
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Deactivate Academic Years')
                        ->modalDescription('Are you sure you want to deactivate the selected academic years? This will also remove their current year status.'),

                    BulkAction::make('mark_completed')
                        ->label('Mark Completed')
                        ->icon('heroicon-o-flag')
                        ->color('gray')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'status' => 'completed',
                                    'is_current' => false,
                                ]);
                            });
                        })
                        ->requiresConfirmation(),

                    ForceDeleteBulkAction::make()
                        ->icon('heroicon-o-trash'),
                    RestoreBulkAction::make()
                        ->icon('heroicon-o-arrow-path'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->searchPlaceholder('Search academic years, schools...')
            ->emptyStateHeading('No Academic Years Found')
            ->emptyStateDescription('Create your first academic year to start managing school sessions.')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->deferLoading();
    }
}
