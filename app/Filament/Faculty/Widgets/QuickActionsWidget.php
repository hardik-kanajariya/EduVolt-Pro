<?php

namespace App\Filament\Faculty\Widgets;

use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Student;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class QuickActionsWidget extends Widget
{
    protected static string $view = 'filament.faculty.widgets.quick-actions-widget';

    protected int | string | array $columnSpan = 'full';

    public function getTodayClasses()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return collect();
        }

        return \App\Models\Timetable::where('teacher_id', $teacher->id)
            ->where('day_of_week', now()->dayOfWeek)
            ->with('schoolClass')
            ->get();
    }

    public function getPendingAssignments()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return 0;
        }

        return Assignment::where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->where('due_date', '>=', now())
            ->count();
    }

    public function getUnmarkedAttendance()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return collect();
        }

        $todayClasses = \App\Models\Timetable::where('teacher_id', $teacher->id)
            ->where('day_of_week', now()->dayOfWeek)
            ->pluck('class_id');

        $classesWithoutAttendance = [];

        foreach ($todayClasses as $classId) {
            $hasAttendance = Attendance::whereHas('student', function ($query) use ($classId) {
                $query->where('class_id', $classId);
            })
                ->where('date', now()->format('Y-m-d'))
                ->exists();

            if (!$hasAttendance) {
                $class = \App\Models\SchoolClass::find($classId);
                $classesWithoutAttendance[] = $class;
            }
        }

        return collect($classesWithoutAttendance);
    }
}
