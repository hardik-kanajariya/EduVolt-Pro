<?php

namespace App\Filament\Faculty\Pages;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class MarkAttendance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static string $view = 'filament.faculty.pages.mark-attendance';

    public Collection $students;
    public ?int $selectedClass = null;
    public ?string $selectedDate = null;
    public array $attendanceData = [];

    public function mount(): void
    {
        $this->students = new Collection();
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function loadStudents(): void
    {
        if (!$this->selectedClass || !$this->selectedDate) {
            $this->students = new Collection();
            return;
        }

        $this->students = Student::where('class_id', $this->selectedClass)
            ->with(['attendance' => function ($query) {
                $query->whereDate('date', $this->selectedDate);
            }])
            ->get();

        // Initialize attendance data
        $this->attendanceData = [];
        foreach ($this->students as $student) {
            $existingAttendance = $student->attendance->first();
            $this->attendanceData[$student->id] = [
                'status' => $existingAttendance?->status ?? 'present',
                'remarks' => $existingAttendance?->remarks ?? '',
            ];
        }
    }

    public function markAttendance(): void
    {
        if (!$this->selectedClass || !$this->selectedDate) {
            Notification::make()
                ->title('Please select class and date')
                ->danger()
                ->send();
            return;
        }

        $markedCount = 0;
        foreach ($this->attendanceData as $studentId => $attendanceInfo) {
            Attendance::updateOrCreate([
                'student_id' => $studentId,
                'date' => $this->selectedDate,
            ], [
                'class_id' => $this->selectedClass,
                'status' => $attendanceInfo['status'],
                'remarks' => $attendanceInfo['remarks'],
                'marked_by' => Auth::id(),
            ]);
            $markedCount++;
        }

        Notification::make()
            ->title("Attendance marked for {$markedCount} students")
            ->success()
            ->send();
    }

    public function updateAttendanceStatus(int $studentId, string $status): void
    {
        $this->attendanceData[$studentId]['status'] = $status;
    }

    public function updateAttendanceRemarks(int $studentId, string $remarks): void
    {
        $this->attendanceData[$studentId]['remarks'] = $remarks;
    }

    public function getClassOptions(): array
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) return [];

        return SchoolClass::whereHas('subjects.teachers', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->pluck('name', 'id')->toArray();
    }
}
