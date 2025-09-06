<?php

namespace App\Filament\Student\Widgets;

use App\Models\Student;
use App\Models\Assignment;
use App\Models\FeeInstallment;
use App\Models\Exam;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class NotificationsWidget extends Widget
{
    protected static string $view = 'filament.student.widgets.notifications-widget';

    protected int | string | array $columnSpan = 'full';

    public function getOverdueAssignments()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return collect();
        }

        return Assignment::where('class_id', $student->class_id)
            ->where('due_date', '<', now())
            ->whereDoesntHave('submissions', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with('subject')
            ->get();
    }

    public function getUpcomingExams()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return collect();
        }

        return Exam::whereHas('examSubjects.subject.classes', function ($query) use ($student) {
            $query->where('class_id', $student->class_id);
        })
            ->where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addDays(7))
            ->with('examSubjects.subject')
            ->get();
    }

    public function getPendingFees()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return collect();
        }

        return FeeInstallment::whereHas('studentFeeAssignment', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->where('status', 'pending')
            ->where('due_date', '<=', now()->addDays(7))
            ->with('studentFeeAssignment.feeStructure.feeCategory')
            ->get();
    }

    public function getRecentGrades()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return collect();
        }

        return \App\Models\Grade::where('student_id', $student->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->with('subject')
            ->latest()
            ->take(3)
            ->get();
    }
}
