<?php

namespace App\Filament\Admin\Resources\Attendances\Schemas;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\AttendanceSession;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class AttendanceForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                Select::make('student_id')
                    ->label('Student')
                    ->relationship('student', 'first_name')
                    ->getOptionLabelFromRecordUsing(
                        fn(Student $record): string =>
                        "{$record->first_name} {$record->last_name} ({$record->admission_number})"
                    )
                    ->searchable(['first_name', 'last_name', 'admission_number'])
                    ->preload()
                    ->required()
                    ->live()
                    ->prefixIcon('heroicon-m-user')
                    ->helperText('Search by name or admission number')
                    ->afterStateUpdated(function ($set, $state) {
                        if ($state) {
                            $student = Student::find($state);
                            if ($student && $student->school_class_id) {
                                $set('class_id', $student->school_class_id);
                            }
                        }
                    })
                    ->columnSpan(2),

                Select::make('class_id')
                    ->label('Class')
                    ->relationship('schoolClass', 'name')
                    ->getOptionLabelFromRecordUsing(
                        fn(SchoolClass $record): string =>
                        "{$record->name} - {$record->section}"
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->prefixIcon('heroicon-m-academic-cap')
                    ->helperText('Will auto-fill based on student selection')
                    ->columnSpan(2),
                DatePicker::make('date')
                    ->label('Date')
                    ->default(now())
                    ->required()
                    ->native(false)
                    ->maxDate(now())
                    ->prefixIcon('heroicon-m-calendar-days')
                    ->helperText('Date of attendance record')
                    ->live()
                    ->afterStateUpdated(function ($set, $state, $get) {
                        // Check if attendance already exists for this student and date
                        if ($state && $get('student_id')) {
                            $existing = \App\Models\Attendance::where('student_id', $get('student_id'))
                                ->where('date', $state)
                                ->exists();

                            if ($existing) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Attendance Already Exists')
                                    ->body('An attendance record already exists for this student on this date.')
                                    ->warning()
                                    ->send();
                            }
                        }
                    })
                    ->columnSpan(1),

                Select::make('status')
                    ->label('Attendance Status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'excused' => 'Excused',
                        'half_day' => 'Half Day',
                    ])
                    ->default('present')
                    ->required()
                    ->live()
                    ->prefixIcon('heroicon-m-check-circle')
                    ->afterStateUpdated(function ($set, $state) {
                        // Auto-set times based on status
                        if ($state === 'present') {
                            $set('in_time', '08:00');
                            $set('out_time', '15:00');
                        } elseif ($state === 'late') {
                            $set('in_time', '08:30');
                            $set('out_time', '15:00');
                        } elseif ($state === 'half_day') {
                            $set('in_time', '08:00');
                            $set('out_time', '12:00');
                        } else {
                            $set('in_time', null);
                            $set('out_time', null);
                        }
                    })
                    ->columnSpan(1),

                Select::make('session_id')
                    ->label('Session')
                    ->relationship('session', 'session_type')
                    ->getOptionLabelFromRecordUsing(
                        fn(AttendanceSession $record): string =>
                        ucfirst($record->session_type) . " Session"
                    )
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->prefixIcon('heroicon-m-clock')
                    ->helperText('Optional: Select attendance session')
                    ->columnSpan(2),
                TimePicker::make('in_time')
                    ->label('Check-in Time')
                    ->seconds(false)
                    ->prefixIcon('heroicon-m-arrow-right-on-rectangle')
                    ->helperText('Time when student arrived')
                    ->visible(fn($get) => in_array($get('status'), ['present', 'late', 'half_day']))
                    ->columnSpan(1),

                TimePicker::make('out_time')
                    ->label('Check-out Time')
                    ->seconds(false)
                    ->prefixIcon('heroicon-m-arrow-left-on-rectangle')
                    ->helperText('Time when student left')
                    ->visible(fn($get) => in_array($get('status'), ['present', 'late', 'half_day']))
                    ->after('in_time')
                    ->columnSpan(1),

                Toggle::make('notify_parents')
                    ->label('Notify Parents')
                    ->helperText('Send notification to parents about attendance')
                    ->default(fn($get) => $get('status') === 'absent')
                    ->visible(fn($get) => in_array($get('status'), ['absent', 'late']))
                    ->columnSpan(2),
                Textarea::make('remarks')
                    ->label('Remarks')
                    ->placeholder('Add any additional notes about the attendance...')
                    ->rows(3)
                    ->maxLength(500)
                    ->helperText('Optional remarks or notes')
                    ->columnSpanFull(),

                Select::make('marked_by')
                    ->label('Marked By')
                    ->relationship('markedBy', 'name')
                    ->default(Auth::id())
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->prefixIcon('heroicon-m-user-circle')
                    ->helperText('User who is marking attendance')
                    ->columnSpan(2),

                Placeholder::make('attendance_summary')
                    ->label('Student Attendance Summary')
                    ->content(function ($get): HtmlString {
                        $studentId = $get('student_id');
                        $date = $get('date');

                        if (!$studentId || !$date) {
                            return new HtmlString('<div class="p-4 bg-gray-50 rounded-lg text-gray-500">Select student and date to see attendance summary</div>');
                        }

                        $monthStart = \Carbon\Carbon::parse($date)->startOfMonth();
                        $monthEnd = \Carbon\Carbon::parse($date)->endOfMonth();

                        $totalDays = \App\Models\Attendance::where('student_id', $studentId)
                            ->whereBetween('date', [$monthStart, $monthEnd])
                            ->count();

                        $presentDays = \App\Models\Attendance::where('student_id', $studentId)
                            ->whereBetween('date', [$monthStart, $monthEnd])
                            ->where('status', 'present')
                            ->count();

                        $absentDays = \App\Models\Attendance::where('student_id', $studentId)
                            ->whereBetween('date', [$monthStart, $monthEnd])
                            ->where('status', 'absent')
                            ->count();

                        $lateDays = \App\Models\Attendance::where('student_id', $studentId)
                            ->whereBetween('date', [$monthStart, $monthEnd])
                            ->where('status', 'late')
                            ->count();

                        $percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

                        return new HtmlString('<div class="p-4 bg-blue-50 rounded-lg border border-blue-200">' .
                            '<h4 class="font-semibold text-blue-900 mb-2">Monthly Attendance Summary</h4>' .
                            '<div class="grid grid-cols-2 gap-4 text-sm">' .
                            '<div><strong>Total Days:</strong> ' . $totalDays . '</div>' .
                            '<div><strong>Present:</strong> ' . $presentDays . ' days</div>' .
                            '<div><strong>Absent:</strong> ' . $absentDays . ' days</div>' .
                            '<div><strong>Late:</strong> ' . $lateDays . ' days</div>' .
                            '<div class="col-span-2 pt-2 border-t border-blue-200">' .
                            '<strong>Attendance Rate:</strong> <span class="' .
                            ($percentage >= 90 ? 'text-green-600' : ($percentage >= 75 ? 'text-yellow-600' : 'text-red-600')) .
                            '">' . $percentage . '%</span>' .
                            '</div>' .
                            '</div>' .
                            '</div>');
                    })
                    ->columnSpan(2),
            ])
            ->columns(1);
    }
}
