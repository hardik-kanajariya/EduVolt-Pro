<?php

namespace App\Filament\Admin\Resources\Attendances\Pages;

use App\Filament\Admin\Resources\Attendances\AttendanceResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Attendance Recorded')
            ->body('Student attendance has been successfully recorded.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the current user as the one marking attendance
        $data['marked_by'] = Auth::id();

        // Auto-set times based on status if not provided
        if (!isset($data['in_time']) && in_array($data['status'], ['present', 'late'])) {
            $data['in_time'] = $data['status'] === 'late' ? '08:30:00' : '08:00:00';
        }

        if (!isset($data['out_time']) && $data['status'] === 'present') {
            $data['out_time'] = '15:00:00';
        }

        // Check for duplicate attendance
        $existingAttendance = \App\Models\Attendance::where('student_id', $data['student_id'])
            ->where('date', $data['date'])
            ->first();

        if ($existingAttendance) {
            Notification::make()
                ->warning()
                ->title('Duplicate Attendance')
                ->body('Attendance for this student on this date already exists. Updating existing record.')
                ->persistent()
                ->send();

            // Update existing record instead of creating new one
            $existingAttendance->update($data);
            $this->record = $existingAttendance;
            return [];
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Log the attendance creation
        Log::info('Attendance record created', [
            'attendance_id' => $this->record->id,
            'student_id' => $this->record->student_id,
            'student_name' => $this->record->student->first_name . ' ' . $this->record->student->last_name,
            'class_id' => $this->record->class_id,
            'date' => $this->record->date,
            'status' => $this->record->status,
            'marked_by' => Auth::id(),
        ]);

        // Send notifications based on status
        if ($this->record->status === 'absent') {
            Notification::make()
                ->title('Absence Recorded')
                ->body("Student {$this->record->student->first_name} {$this->record->student->last_name} is marked absent for " . \Carbon\Carbon::parse($this->record->date)->format('M j, Y'))
                ->warning()
                ->persistent()
                ->send();
        } elseif ($this->record->status === 'late') {
            Notification::make()
                ->title('Late Arrival Recorded')
                ->body("Student {$this->record->student->first_name} {$this->record->student->last_name} arrived late on " . \Carbon\Carbon::parse($this->record->date)->format('M j, Y'))
                ->warning()
                ->send();
        }

        // Check attendance pattern and send alerts
        $this->checkAttendancePattern();
    }

    protected function checkAttendancePattern(): void
    {
        $studentId = $this->record->student_id;
        $currentDate = $this->record->date;

        // Check for consecutive absences
        $recentAbsences = \App\Models\Attendance::where('student_id', $studentId)
            ->where('status', 'absent')
            ->where('date', '<=', $currentDate)
            ->orderBy('date', 'desc')
            ->limit(3)
            ->count();

        if ($recentAbsences >= 3 && $this->record->status === 'absent') {
            Notification::make()
                ->title('Attendance Alert')
                ->body("Student {$this->record->student->first_name} {$this->record->student->last_name} has been absent for 3 or more consecutive days.")
                ->danger()
                ->persistent()
                ->send();
        }

        // Check monthly attendance percentage
        $monthStart = \Carbon\Carbon::parse($currentDate)->startOfMonth();
        $monthEnd = \Carbon\Carbon::parse($currentDate)->endOfMonth();

        $totalDays = \App\Models\Attendance::where('student_id', $studentId)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->count();

        $presentDays = \App\Models\Attendance::where('student_id', $studentId)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->where('status', 'present')
            ->count();

        if ($totalDays >= 10 && $presentDays > 0) {
            $percentage = ($presentDays / $totalDays) * 100;

            if ($percentage < 75) {
                Notification::make()
                    ->title('Low Attendance Warning')
                    ->body("Student {$this->record->student->first_name} {$this->record->student->last_name} has {$percentage}% attendance this month (below 75% threshold).")
                    ->warning()
                    ->persistent()
                    ->send();
            }
        }
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Record Attendance')
                ->icon('heroicon-m-check-circle'),

            $this->getCancelFormAction()
                ->label('Cancel')
                ->icon('heroicon-m-x-mark'),

            Action::make('create_and_create_another')
                ->label('Record & Add Another')
                ->icon('heroicon-m-plus')
                ->color('gray')
                ->action('create')
                ->after(function () {
                    $this->redirect($this->getResource()::getUrl('create'));
                }),

            Action::make('bulk_attendance')
                ->label('Bulk Attendance')
                ->icon('heroicon-m-queue-list')
                ->color('info')
                ->url(fn(): string => $this->getResource()::getUrl('create') . '?bulk=true')
                ->openUrlInNewTab(),
        ];
    }
}
