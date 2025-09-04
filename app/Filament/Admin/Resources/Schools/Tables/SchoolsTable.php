<?php

namespace App\Filament\Admin\Resources\Schools\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class SchoolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->defaultImageUrl('/images/default-school.png'),

                TextColumn::make('name')
                    ->label('School Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->address)
                    ->limit(30)
                    ->tooltip(fn($record) => $record->address),

                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('School code copied!')
                    ->copyMessageDuration(1500),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'public' => 'success',
                        'private' => 'info',
                        'charter' => 'warning',
                        'magnet' => 'primary',
                        'international' => 'secondary',
                        'religious' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'public' => 'Public',
                        'private' => 'Private',
                        'charter' => 'Charter',
                        'magnet' => 'Magnet',
                        'international' => 'International',
                        'religious' => 'Religious',
                        default => $state,
                    }),

                TextColumn::make('phone')
                    ->label('Contact')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->copyable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->copyable(),

                TextColumn::make('students_count')
                    ->label('Students')
                    ->counts('students')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('teachers_count')
                    ->label('Teachers')
                    ->counts('teachers')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('classes_count')
                    ->label('Classes')
                    ->counts('classes')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('established_date')
                    ->label('Established')
                    ->date('M j, Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'pending' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'pending' => 'Pending',
                        'suspended' => 'Suspended',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),

                SelectFilter::make('type')
                    ->label('School Type')
                    ->options([
                        'public' => 'Public School',
                        'private' => 'Private School',
                        'charter' => 'Charter School',
                        'magnet' => 'Magnet School',
                        'international' => 'International School',
                        'religious' => 'Religious School',
                    ])
                    ->multiple(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'pending' => 'Pending',
                        'suspended' => 'Suspended',
                    ])
                    ->multiple(),

                TernaryFilter::make('has_students')
                    ->label('Has Students')
                    ->queries(
                        true: fn($query) => $query->whereHas('students'),
                        false: fn($query) => $query->whereDoesntHave('students'),
                    ),

                TernaryFilter::make('has_teachers')
                    ->label('Has Teachers')
                    ->queries(
                        true: fn($query) => $query->whereHas('teachers'),
                        false: fn($query) => $query->whereDoesntHave('teachers'),
                    ),

                Filter::make('established_range')
                    ->form([
                        DatePicker::make('established_from')
                            ->label('Established From'),
                        DatePicker::make('established_until')
                            ->label('Established Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['established_from'], fn(Builder $query, $date): Builder => $query->whereDate('established_date', '>=', $date))
                            ->when($data['established_until'], fn(Builder $query, $date): Builder => $query->whereDate('established_date', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->label('New School')
                    ->icon('heroicon-o-plus'),
            ])
            ->recordBulkActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray'),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('name')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->striped()
            ->searchOnBlur()
            ->deferLoading()
            ->poll('60s');
    }
}
