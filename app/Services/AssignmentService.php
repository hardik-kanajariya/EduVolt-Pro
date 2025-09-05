<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AssignmentDueReminder;
use Illuminate\Support\Facades\Auth;

class AssignmentService
{
    /**
     * Get assignments for a specific class
     */
    public function getClassAssignments(int $classId, string $status = 'published'): Collection
    {
        return Assignment::where('class_id', $classId)
            ->where('status', $status)
            ->with(['teacher', 'subject'])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Get student submissions for an assignment
     */
    public function getAssignmentSubmissions(int $assignmentId): Collection
    {
        return AssignmentSubmission::where('assignment_id', $assignmentId)
            ->with(['student'])
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    /**
     * Submit assignment for a student
     */
    public function submitAssignment(int $assignmentId, int $studentId, array $data): AssignmentSubmission
    {
        $assignment = Assignment::findOrFail($assignmentId);
        
        // Check if already submitted
        $existingSubmission = AssignmentSubmission::where([
            'assignment_id' => $assignmentId,
            'student_id' => $studentId,
        ])->first();

        if ($existingSubmission) {
            // Update existing submission
            $existingSubmission->update([
                'content' => $data['content'],
                'attachments' => $data['attachments'] ?? null,
                'submitted_at' => now(),
                'status' => 'resubmitted',
            ]);
            
            return $existingSubmission;
        }

        // Create new submission
        return AssignmentSubmission::create([
            'assignment_id' => $assignmentId,
            'student_id' => $studentId,
            'content' => $data['content'],
            'attachments' => $data['attachments'] ?? null,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);
    }

    /**
     * Grade assignment submission
     */
    public function gradeSubmission(int $submissionId, array $data): AssignmentSubmission
    {
        $submission = AssignmentSubmission::findOrFail($submissionId);
        
        $submission->update([
            'marks_obtained' => $data['marks_obtained'],
            'feedback' => $data['feedback'] ?? null,
            'status' => 'graded',
            'graded_at' => now(),
            'graded_by' => Auth::id(),
        ]);

        return $submission;
    }

    /**
     * Get overdue assignments
     */
    public function getOverdueAssignments(): Collection
    {
        return Assignment::where('status', 'published')
            ->where('due_date', '<', now())
            ->with(['schoolClass', 'subject', 'teacher'])
            ->get();
    }

    /**
     * Get assignments due soon (within next 24 hours)
     */
    public function getAssignmentsDueSoon(): Collection
    {
        return Assignment::where('status', 'published')
            ->whereBetween('due_date', [now(), now()->addDay()])
            ->with(['schoolClass', 'subject', 'teacher'])
            ->get();
    }

    /**
     * Send due date reminders
     */
    public function sendDueReminders(): void
    {
        $assignmentsDueSoon = $this->getAssignmentsDueSoon();

        foreach ($assignmentsDueSoon as $assignment) {
            $students = Student::where('class_id', $assignment->class_id)
                ->where('status', 'active')
                ->with('user')
                ->get();

            foreach ($students as $student) {
                if ($student->user && $student->user->email) {
                    // Check if not already submitted
                    $submission = AssignmentSubmission::where([
                        'assignment_id' => $assignment->id,
                        'student_id' => $student->id,
                    ])->first();

                    if (!$submission) {
                        Notification::send($student->user, new AssignmentDueReminder($assignment));
                    }
                }
            }
        }
    }

    /**
     * Get assignment statistics for a class
     */
    public function getClassAssignmentStats(int $classId): array
    {
        $totalAssignments = Assignment::where('class_id', $classId)
            ->where('status', 'published')
            ->count();

        $activeAssignments = Assignment::where('class_id', $classId)
            ->where('status', 'published')
            ->where('due_date', '>=', now())
            ->count();

        $overdueAssignments = Assignment::where('class_id', $classId)
            ->where('status', 'published')
            ->where('due_date', '<', now())
            ->count();

        $totalSubmissions = AssignmentSubmission::whereHas('assignment', function ($query) use ($classId) {
            $query->where('class_id', $classId);
        })->count();

        return [
            'total_assignments' => $totalAssignments,
            'active_assignments' => $activeAssignments,
            'overdue_assignments' => $overdueAssignments,
            'total_submissions' => $totalSubmissions,
        ];
    }
}
