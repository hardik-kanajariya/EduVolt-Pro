<?php

namespace App\Listeners;

use App\Services\StudentProgressService;
use App\Models\AssignmentSubmission;
use App\Models\ExamMark;
use App\Models\Attendance;
use App\Models\AcademicYear;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateProgressOnAssignmentSubmission implements ShouldQueue
{
    use InteractsWithQueue;

    protected StudentProgressService $progressService;

    public function __construct(StudentProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            if (method_exists($event, 'submission') && $event->submission instanceof AssignmentSubmission) {
                $this->handleAssignmentSubmission($event->submission);
            } elseif (method_exists($event, 'examMark') && $event->examMark instanceof ExamMark) {
                $this->handleExamMark($event->examMark);
            } elseif (method_exists($event, 'attendance') && $event->attendance instanceof Attendance) {
                $this->handleAttendance($event->attendance);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update student progress automatically', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function handleAssignmentSubmission(AssignmentSubmission $submission): void
    {
        $assignment = $submission->assignment;
        $academicYear = AcademicYear::where('is_current', true)->first();
        
        if (!$academicYear) {
            return;
        }

        $this->progressService->updateStudentProgress(
            $submission->student_id,
            $academicYear->id,
            $assignment->subject_id,
            $assignment->class_id,
            'first' // Default term, could be dynamic
        );
    }

    protected function handleExamMark(ExamMark $examMark): void
    {
        $exam = $examMark->exam;
        $academicYear = AcademicYear::where('is_current', true)->first();
        
        if (!$academicYear) {
            return;
        }

        $this->progressService->updateStudentProgress(
            $examMark->student_id,
            $academicYear->id,
            $exam->subject_id,
            $exam->class_id,
            'first' // Default term, could be dynamic
        );
    }

    protected function handleAttendance(Attendance $attendance): void
    {
        $academicYear = AcademicYear::where('is_current', true)->first();
        
        if (!$academicYear) {
            return;
        }

        $this->progressService->updateStudentProgress(
            $attendance->student_id,
            $academicYear->id,
            $attendance->subject_id,
            $attendance->class_id,
            'first' // Default term, could be dynamic
        );
    }
}
