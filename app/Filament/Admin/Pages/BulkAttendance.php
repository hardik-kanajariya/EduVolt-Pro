<?php

namespace App\Filament\Admin\Pages;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BulkAttendance extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static string $view = 'filament.admin.pages.bulk-attendance';

    protected static ?string $navigationLabel = 'Bulk Attendance';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public ?SchoolClass $selectedClass = null;

    public ?array $students = [];

    public function mount(): void
    {
        $this->form->fill([
            'date' => now()->toDateString(),
            'session_id' => AttendanceSession::first()?->id,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Attendance Details')
                    ->schema([
                        Forms\Components\Select::make('class_id')
                            ->label('Class')
                            ->options(SchoolClass::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn($state) => $this->loadStudents($state)),

                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->default(now()),

                        Forms\Components\Select::make('session_id')
                            ->label('Session')
                            ->options(AttendanceSession::pluck('name', 'id'))
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Students')
                    ->schema([
                        Forms\Components\Repeater::make('attendance')
                            ->schema([
                                Forms\Components\Hidden::make('student_id'),

                                Forms\Components\TextInput::make('student_name')
                                    ->label('Student Name')
                                    ->disabled(),

                                Forms\Components\TextInput::make('admission_number')
                                    ->label('Admission No.')
                                    ->disabled(),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'present' => 'Present',
                                        'absent' => 'Absent',
                                        'late' => 'Late',
                                        'partial' => 'Partial',
                                        'excused' => 'Excused',
                                    ])
                                    ->default('present')
                                    ->required(),

                                Forms\Components\TimePicker::make('in_time')
                                    ->label('Time In'),

                                Forms\Components\TimePicker::make('out_time')
                                    ->label('Time Out'),

                                Forms\Components\TextInput::make('remarks')
                                    ->maxLength(255),
                            ])
                            ->columns(7)
                            ->reorderable(false)
                            ->addable(false)
                            ->deletable(false)
                            ->default([]),
                    ])
                    ->visible(fn() => !empty($this->data['attendance'])),
            ])
            ->statePath('data');
    }

    public function loadStudents(?int $classId): void
    {
        if (!$classId) {
            $this->data['attendance'] = [];
            return;
        }

        $students = Student::where('class_id', $classId)
            ->where('status', 'active')
            ->orderBy('roll_number')
            ->get();

        $attendance = [];
        foreach ($students as $student) {
            $attendance[] = [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'admission_number' => $student->admission_number,
                'status' => 'present',
                'in_time' => null,
                'out_time' => null,
                'remarks' => null,
            ];
        }

        $this->data['attendance'] = $attendance;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Attendance')
                ->action('saveAttendance')
                ->color('success'),

            Action::make('mark_all_present')
                ->label('Mark All Present')
                ->action('markAllPresent')
                ->color('info'),

            Action::make('reset')
                ->label('Reset')
                ->action('resetForm')
                ->color('gray'),
        ];
    }

    public function saveAttendance(): void
    {
        $data = $this->form->getState();

        if (empty($data['attendance'])) {
            Notification::make()
                ->title('No students found')
                ->danger()
                ->send();
            return;
        }

        DB::transaction(function () use ($data) {
            foreach ($data['attendance'] as $attendanceData) {
                // Check if attendance already exists for this date
                $existingAttendance = Attendance::where([
                    'student_id' => $attendanceData['student_id'],
                    'class_id' => $data['class_id'],
                    'date' => $data['date'],
                    'session_id' => $data['session_id'],
                ])->first();

                if ($existingAttendance) {
                    // Update existing attendance
                    $existingAttendance->update([
                        'status' => $attendanceData['status'],
                        'in_time' => $attendanceData['in_time'],
                        'out_time' => $attendanceData['out_time'],
                        'remarks' => $attendanceData['remarks'],
                        'marked_by' => Auth::id(),
                    ]);
                } else {
                    // Create new attendance record
                    Attendance::create([
                        'student_id' => $attendanceData['student_id'],
                        'class_id' => $data['class_id'],
                        'session_id' => $data['session_id'],
                        'date' => $data['date'],
                        'status' => $attendanceData['status'],
                        'in_time' => $attendanceData['in_time'],
                        'out_time' => $attendanceData['out_time'],
                        'remarks' => $attendanceData['remarks'],
                        'marked_by' => Auth::id(),
                    ]);
                }
            }
        });

        Notification::make()
            ->title('Attendance saved successfully')
            ->success()
            ->send();
    }

    public function markAllPresent(): void
    {
        if (!empty($this->data['attendance'])) {
            foreach ($this->data['attendance'] as $index => $attendance) {
                $this->data['attendance'][$index]['status'] = 'present';
            }

            Notification::make()
                ->title('All students marked present')
                ->success()
                ->send();
        }
    }

    public function resetForm(): void
    {
        $this->data = [
            'date' => now()->toDateString(),
            'session_id' => AttendanceSession::first()?->id,
            'attendance' => [],
        ];

        Notification::make()
            ->title('Form reset successfully')
            ->success()
            ->send();
    }
}
