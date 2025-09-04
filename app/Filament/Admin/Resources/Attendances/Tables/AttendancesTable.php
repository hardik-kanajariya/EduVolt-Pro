<?php

namespace App\Filament\Admin\Resources\Attendances\Tables;

use App\Models\SchoolClass;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.admission_number')
                    ->label('Admission No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student.first_name')
                    ->label('Student Name')
                    ->formatStateUsing(fn($record) => $record->student ? "{$record->student->first_name} {$record->student->last_name}" : 'N/A')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->formatStateUsing(fn($record) => $record->schoolClass ? "{$record->schoolClass->name} - {$record->schoolClass->section}" : 'N/A')
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'present',
                        'danger' => 'absent',
                        'warning' => 'late',
                        'info' => 'excused',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'present',
                        'heroicon-o-x-circle' => 'absent',
                        'heroicon-o-clock' => 'late',
                        'heroicon-o-exclamation-circle' => 'excused',
                    ]),

                TextColumn::make('in_time')
                    ->label('In Time')
                    ->time('H:i')
                    ->placeholder('Not set'),

                TextColumn::make('out_time')
                    ->label('Out Time')
                    ->time('H:i')
                    ->placeholder('Not set'),

                TextColumn::make('session.session_type')
                    ->label('Session')
                    ->formatStateUsing(fn($state) => $state ? ucfirst($state) : 'N/A')
                    ->toggleable(),

                TextColumn::make('markedBy.name')
                    ->label('Marked By')
                    ->toggleable(),

                TextColumn::make('remarks')
                    ->label('Remarks')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'excused' => 'Excused',
                    ]),

                SelectFilter::make('class_id')
                    ->label('Class')
                    ->relationship('schoolClass', 'name')
                    ->getOptionLabelFromRecordUsing(fn(SchoolClass $record): string => "{$record->name} - {$record->section}"),

                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from_date')
                            ->label('From Date'),
                        DatePicker::make('to_date')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }
}
