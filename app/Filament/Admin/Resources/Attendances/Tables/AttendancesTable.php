<?php

namespace App\Filament\Admin\Resources\Attendances\Tables;

use App\Models\SchoolClass;
use App\Models\Student;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Student Information
                TextColumn::make('student.admission_number')
                    ->label('Admission No.')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable()
                    ->copyMessage('Admission number copied')
                    ->icon('heroicon-m-identification')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('student.full_name')
                    ->label('Student Name')
                    ->state(function ($record): string {
                        return $record->student
                            ? "{$record->student->first_name} {$record->student->last_name}"
                            : 'N/A';
                    })
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight('medium')
                    ->icon('heroicon-m-user'),

                TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->state(function ($record): string {
                        return $record->schoolClass
                            ? "{$record->schoolClass->name} - {$record->schoolClass->section}"
                            : 'N/A';
                    })
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-academic-cap'),

                // Date and Time Information
                TextColumn::make('date')
                    ->label('Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->weight('medium')
                    ->icon('heroicon-m-calendar-days')
                    ->description(
                        fn($record): string =>
                        \Carbon\Carbon::parse($record->date)->diffForHumans()
                    ),

                TextColumn::make('day_of_week')
                    ->label('Day')
                    ->state(function ($record): string {
                        return \Carbon\Carbon::parse($record->date)->format('l');
                    })
                    ->badge()
                    ->color(function ($record): string {
                        $day = \Carbon\Carbon::parse($record->date)->dayOfWeek;
                        return match ($day) {
                            0 => 'danger',  // Sunday
                            6 => 'warning', // Saturday
                            default => 'success',
                        };
                    }),

                // Attendance Status
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'excused' => 'info',
                        'half_day' => 'secondary',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'present' => 'heroicon-m-check-circle',
                        'absent' => 'heroicon-m-x-circle',
                        'late' => 'heroicon-m-clock',
                        'excused' => 'heroicon-m-document-check',
                        'half_day' => 'heroicon-m-adjustments-horizontal',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'half_day' => 'Half Day',
                        default => ucfirst($state),
                    }),

                // Time Tracking
                TextColumn::make('in_time')
                    ->label('Check In')
                    ->time('H:i')
                    ->placeholder('--:--')
                    ->icon('heroicon-m-arrow-right-on-rectangle')
                    ->color('success')
                    ->description(function ($record): ?string {
                        if (!$record->in_time) return null;
                        $schoolStart = '08:00';
                        $checkIn = \Carbon\Carbon::parse($record->in_time);
                        $start = \Carbon\Carbon::parse($schoolStart);

                        if ($checkIn->gt($start)) {
                            $diff = $checkIn->diff($start);
                            return "Late by {$diff->format('%h:%i')}";
                        }
                        return 'On time';
                    }),

                TextColumn::make('out_time')
                    ->label('Check Out')
                    ->time('H:i')
                    ->placeholder('--:--')
                    ->icon('heroicon-m-arrow-left-on-rectangle')
                    ->color('warning')
                    ->toggleable(),

                TextColumn::make('duration')
                    ->label('Duration')
                    ->state(function ($record): string {
                        if (!$record->in_time || !$record->out_time) {
                            return '--';
                        }

                        $in = \Carbon\Carbon::parse($record->in_time);
                        $out = \Carbon\Carbon::parse($record->out_time);
                        $diff = $in->diff($out);

                        return $diff->format('%h:%i hrs');
                    })
                    ->badge()
                    ->color(function ($record): string {
                        if (!$record->in_time || !$record->out_time) return 'gray';

                        $in = \Carbon\Carbon::parse($record->in_time);
                        $out = \Carbon\Carbon::parse($record->out_time);
                        $hours = $in->diffInHours($out);

                        if ($hours >= 7) return 'success';
                        if ($hours >= 4) return 'warning';
                        return 'danger';
                    })
                    ->toggleable(),

                // Session and Context
                TextColumn::make('session.session_type')
                    ->label('Session')
                    ->formatStateUsing(fn($state) => $state ? ucfirst($state) . ' Session' : 'Regular')
                    ->badge()
                    ->color('secondary')
                    ->icon('heroicon-m-clock')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Administrative Information
                TextColumn::make('markedBy.name')
                    ->label('Marked By')
                    ->icon('heroicon-m-user-circle')
                    ->placeholder('System')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('remarks')
                    ->label('Remarks')
                    ->limit(25)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (!$state || strlen($state) <= 25) {
                            return null;
                        }
                        return $state;
                    })
                    ->placeholder('No remarks')
                    ->icon('heroicon-m-chat-bubble-left-ellipsis')
                    ->toggleable(),

                // Attendance Statistics
                TextColumn::make('monthly_percentage')
                    ->label('Monthly %')
                    ->state(function ($record): string {
                        $monthStart = \Carbon\Carbon::parse($record->date)->startOfMonth();
                        $monthEnd = \Carbon\Carbon::parse($record->date)->endOfMonth();

                        $totalDays = \App\Models\Attendance::where('student_id', $record->student_id)
                            ->whereBetween('date', [$monthStart, $monthEnd])
                            ->count();

                        $presentDays = \App\Models\Attendance::where('student_id', $record->student_id)
                            ->whereBetween('date', [$monthStart, $monthEnd])
                            ->where('status', 'present')
                            ->count();

                        if ($totalDays === 0) return '0%';

                        return round(($presentDays / $totalDays) * 100, 1) . '%';
                    })
                    ->badge()
                    ->color(function ($record): string {
                        $monthStart = \Carbon\Carbon::parse($record->date)->startOfMonth();
                        $monthEnd = \Carbon\Carbon::parse($record->date)->endOfMonth();

                        $totalDays = \App\Models\Attendance::where('student_id', $record->student_id)
                            ->whereBetween('date', [$monthStart, $monthEnd])
                            ->count();

                        $presentDays = \App\Models\Attendance::where('student_id', $record->student_id)
                            ->whereBetween('date', [$monthStart, $monthEnd])
                            ->where('status', 'present')
                            ->count();

                        if ($totalDays === 0) return 'gray';

                        $percentage = ($presentDays / $totalDays) * 100;

                        if ($percentage >= 90) return 'success';
                        if ($percentage >= 75) return 'warning';
                        return 'danger';
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // Sort by student attendance percentage
                        return $query->orderBy('student_id', $direction);
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                // Record Metadata
                TextColumn::make('created_at')
                    ->label('Recorded')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-m-clock'),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->since()
                    ->icon('heroicon-m-pencil-square'),
            ])
            ->filters([
                // Status Filter
                SelectFilter::make('status')
                    ->label('Attendance Status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'excused' => 'Excused',
                        'half_day' => 'Half Day',
                    ])
                    ->multiple()
                    ->preload(),

                // Class Filter
                SelectFilter::make('class_id')
                    ->label('Class')
                    ->relationship('schoolClass', 'name')
                    ->getOptionLabelFromRecordUsing(
                        fn(SchoolClass $record): string =>
                        "{$record->name} - {$record->section}"
                    )
                    ->searchable()
                    ->preload()
                    ->multiple(),

                // Student Filter
                SelectFilter::make('student_id')
                    ->label('Student')
                    ->relationship('student', 'first_name')
                    ->getOptionLabelFromRecordUsing(
                        fn(Student $record): string =>
                        "{$record->first_name} {$record->last_name} ({$record->admission_number})"
                    )
                    ->searchable(['first_name', 'last_name', 'admission_number'])
                    ->preload()
                    ->multiple(),

                // Date Range Filter
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from_date')
                            ->label('From Date')
                            ->native(false),
                        DatePicker::make('to_date')
                            ->label('To Date')
                            ->native(false),
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

                // This Week Filter
                Filter::make('this_week')
                    ->query(fn(Builder $query): Builder => $query->whereBetween('date', [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ]))
                    ->toggle(),

                // This Month Filter
                Filter::make('this_month')
                    ->query(fn(Builder $query): Builder => $query->whereBetween('date', [
                        now()->startOfMonth(),
                        now()->endOfMonth(),
                    ]))
                    ->toggle(),

                // Late Arrivals Filter
                Filter::make('late_arrivals')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'late'))
                    ->toggle(),

                // Absent Students Filter
                Filter::make('absent_students')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'absent'))
                    ->toggle(),

                // Weekend Attendance Filter
                Filter::make('weekend_attendance')
                    ->query(function (Builder $query): Builder {
                        return $query->whereRaw('DAYOFWEEK(date) IN (1, 7)'); // Sunday = 1, Saturday = 7
                    })
                    ->toggle(),
            ])
            ->actions([
                ViewAction::make()
                    ->icon('heroicon-m-eye')
                    ->color('info'),
                EditAction::make()
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning'),
                Action::make('mark_present')
                    ->label('Mark Present')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status !== 'present')
                    ->action(function ($record) {
                        $record->update(['status' => 'present']);
                        Notification::make()
                            ->title('Status Updated')
                            ->body('Student marked as present')
                            ->success()
                            ->send();
                    }),
                Action::make('mark_absent')
                    ->label('Mark Absent')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->status !== 'absent')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'absent']);
                        Notification::make()
                            ->title('Status Updated')
                            ->body('Student marked as absent')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon('heroicon-m-trash'),
                    Action::make('bulk_mark_present')
                        ->label('Mark All Present')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $count = $records->count();
                            $records->each->update(['status' => 'present']);
                            Notification::make()
                                ->title('Bulk Update Complete')
                                ->body("{$count} students marked as present")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                    Action::make('bulk_mark_absent')
                        ->label('Mark All Absent')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $count = $records->count();
                            $records->each->update(['status' => 'absent']);
                            Notification::make()
                                ->title('Bulk Update Complete')
                                ->body("{$count} students marked as absent")
                                ->warning()
                                ->send();
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('date', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s')
            ->deferLoading()
            ->recordUrl(
                fn($record): string => route('filament.admin.resources.attendances.attendances.view', $record)
            );
    }
}
