<?php

namespace App\Filament\Admin\Resources\Staff\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class StaffTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Staff Identity
                TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable()
                    ->copyMessage('Name copied to clipboard')
                    ->icon('heroicon-m-user'),

                TextColumn::make('employee_id')
                    ->label('Employee ID')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable()
                    ->icon('heroicon-m-identification')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('position')
                    ->label('Position')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->icon('heroicon-m-briefcase')
                    ->color('primary'),

                TextColumn::make('department')
                    ->label('Department')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'administration' => 'success',
                        'academic affairs' => 'info',
                        'it department' => 'warning',
                        'finance' => 'danger',
                        'human resources' => 'primary',
                        default => 'gray',
                    })
                    ->icon('heroicon-m-building-office'),

                TextColumn::make('school.name')
                    ->label('School')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->wrap()
                    ->icon('heroicon-m-building-office-2'),

                // Employment Details
                TextColumn::make('employment_type')
                    ->label('Type')
                    ->color(fn(string $state): string => match ($state) {
                        'full_time' => 'success',
                        'part_time' => 'warning',
                        'contract' => 'info',
                        'temporary' => 'gray',
                        'intern' => 'primary',
                        'volunteer' => 'secondary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract' => 'Contract',
                        'temporary' => 'Temporary',
                        'intern' => 'Intern',
                        'volunteer' => 'Volunteer',
                        default => ucfirst($state),
                    }),

                TextColumn::make('join_date')
                    ->label('Join Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar-days')
                    ->description(
                        fn($record): string =>
                        \Carbon\Carbon::parse($record->join_date)->diffForHumans()
                    ),

                TextColumn::make('service_duration')
                    ->label('Service')
                    ->state(function ($record): string {
                        $years = \Carbon\Carbon::parse($record->join_date)->diffInYears(now());
                        $months = \Carbon\Carbon::parse($record->join_date)->diffInMonths(now()) % 12;

                        if ($years > 0) {
                            return $months > 0 ? "{$years}y {$months}m" : "{$years} years";
                        }

                        return $months > 0 ? "{$months} months" : "New";
                    })
                    ->badge()
                    ->color(function ($record): string {
                        $years = \Carbon\Carbon::parse($record->join_date)->diffInYears(now());
                        if ($years >= 5) return 'success';
                        if ($years >= 2) return 'warning';
                        return 'gray';
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('join_date', $direction === 'asc' ? 'desc' : 'asc');
                    }),

                TextColumn::make('salary')
                    ->label('Salary')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-m-currency-dollar')
                    ->color('success'),

                // Status Management
                TextColumn::make('status')
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'terminated' => 'danger',
                        'on_leave' => 'info',
                        'resigned' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'active' => 'heroicon-m-check-circle',
                        'inactive' => 'heroicon-m-pause-circle',
                        'terminated' => 'heroicon-m-x-circle',
                        'on_leave' => 'heroicon-m-clock',
                        'resigned' => 'heroicon-m-arrow-right-on-rectangle',
                        default => 'heroicon-m-question-mark-circle',
                    }),

                // Audit Trail
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-m-plus-circle'),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-m-pencil-square')
                    ->since(),

                TextColumn::make('deleted_at')
                    ->label('Deleted')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-m-trash'),
            ])
            ->filters([
                // Department Filter
                SelectFilter::make('department')
                    ->label('Department')
                    ->options([
                        'Administration' => 'Administration',
                        'Academic Affairs' => 'Academic Affairs',
                        'Student Affairs' => 'Student Affairs',
                        'IT Department' => 'IT Department',
                        'Finance' => 'Finance',
                        'Human Resources' => 'Human Resources',
                        'Library' => 'Library',
                        'Maintenance' => 'Maintenance',
                        'Security' => 'Security',
                        'Health Services' => 'Health Services',
                        'Sports & Activities' => 'Sports & Activities',
                    ])
                    ->multiple()
                    ->preload(),

                // Employment Type Filter
                SelectFilter::make('employment_type')
                    ->label('Employment Type')
                    ->options([
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract' => 'Contract',
                        'temporary' => 'Temporary',
                        'intern' => 'Intern',
                        'volunteer' => 'Volunteer',
                    ])
                    ->multiple(),

                // Status Filter
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'terminated' => 'Terminated',
                        'on_leave' => 'On Leave',
                        'resigned' => 'Resigned',
                    ])
                    ->default('active')
                    ->multiple(),

                // School Filter
                SelectFilter::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                // Service Duration Filter
                Filter::make('service_duration')
                    ->form([
                        Select::make('years')
                            ->label('Years of Service')
                            ->options([
                                '0-1' => 'Less than 1 year',
                                '1-2' => '1-2 years',
                                '2-5' => '2-5 years',
                                '5-10' => '5-10 years',
                                '10+' => '10+ years',
                            ])
                            ->placeholder('Select service duration'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['years'],
                            function (Builder $query, $years) {
                                $now = now();
                                return match ($years) {
                                    '0-1' => $query->whereDate('join_date', '>=', $now->copy()->subYear()),
                                    '1-2' => $query->whereDate('join_date', '>=', $now->copy()->subYears(2))
                                        ->whereDate('join_date', '<', $now->copy()->subYear()),
                                    '2-5' => $query->whereDate('join_date', '>=', $now->copy()->subYears(5))
                                        ->whereDate('join_date', '<', $now->copy()->subYears(2)),
                                    '5-10' => $query->whereDate('join_date', '>=', $now->copy()->subYears(10))
                                        ->whereDate('join_date', '<', $now->copy()->subYears(5)),
                                    '10+' => $query->whereDate('join_date', '<', $now->copy()->subYears(10)),
                                    default => $query,
                                };
                            }
                        );
                    }),

                // Join Date Range Filter
                Filter::make('join_date_range')
                    ->form([
                        DatePicker::make('join_from')
                            ->label('Joined From')
                            ->native(false),
                        DatePicker::make('join_until')
                            ->label('Joined Until')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['join_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('join_date', '>=', $date),
                            )
                            ->when(
                                $data['join_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('join_date', '<=', $date),
                            );
                    }),

                // Soft Delete Filter
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make()
                    ->icon('heroicon-m-eye')
                    ->color('info'),
                EditAction::make()
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon('heroicon-m-trash'),
                    RestoreBulkAction::make()
                        ->icon('heroicon-m-arrow-uturn-left'),
                    ForceDeleteBulkAction::make()
                        ->icon('heroicon-m-x-mark'),
                ]),
            ])
            ->defaultSort('join_date', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('60s')
            ->deferLoading()
            ->recordUrl(
                fn($record): string => route('filament.admin.resources.staffs.view', $record)
            );
    }
}
