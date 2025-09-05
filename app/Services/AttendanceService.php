<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Student;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AttendanceService
{
    /**
     * Mark attendance for multiple students at once
     */
    public function markBulkAttendance(array $attendanceData): array
    {
        $results = [];

        DB::transaction(function () use ($attendanceData, &$results) {
            foreach ($attendanceData['attendance'] as $studentAttendance) {
                $result = $this->markStudentAttendance([
                    'student_id' => $studentAttendance['student_id'],
                    'class_id' => $attendanceData['class_id'],
                    'session_id' => $attendanceData['session_id'],
                    'date' => $attendanceData['date'],
                    'status' => $studentAttendance['status'],
                    'in_time' => $studentAttendance['in_time'] ?? null,
                    'out_time' => $studentAttendance['out_time'] ?? null,
                    'remarks' => $studentAttendance['remarks'] ?? null,
                    'marked_by' => Auth::id(), // Set the user who marked the attendance
                ]);

                $results[] = $result;
            }
        });

        return $results;
    }

    /**
     * Mark attendance for a single student
     */
    public function markStudentAttendance(array $data): Attendance
    {
        // Check if attendance already exists for this date
        $existingAttendance = Attendance::where([
            'student_id' => $data['student_id'],
            'class_id' => $data['class_id'],
            'date' => $data['date'],
            'session_id' => $data['session_id'],
        ])->first();

        if ($existingAttendance) {
            $existingAttendance->update($data);
            return $existingAttendance;
        }

        return Attendance::create($data);
    }

    /**
     * Get attendance for a class on a specific date
     */
    public function getClassAttendance(int $classId, string $date, ?int $sessionId = null): Collection
    {
        $query = Attendance::where('class_id', $classId)
            ->whereDate('date', $date)
            ->with(['student', 'session']);

        if ($sessionId) {
            $query->where('session_id', $sessionId);
        }

        return $query->get();
    }

    /**
     * Get student attendance history
     */
    public function getStudentAttendanceHistory(int $studentId, ?string $fromDate = null, ?string $toDate = null): Collection
    {
        $query = Attendance::where('student_id', $studentId)
            ->with(['session', 'markedBy']);

        if ($fromDate) {
            $query->whereDate('date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('date', '<=', $toDate);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    /**
     * Calculate attendance percentage for a student
     */
    public function calculateAttendancePercentage(int $studentId, ?string $fromDate = null, ?string $toDate = null): array
    {
        $query = Attendance::where('student_id', $studentId);

        if ($fromDate) {
            $query->whereDate('date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('date', '<=', $toDate);
        }

        $totalDays = $query->count();
        $presentDays = $query->where('status', 'present')->count();
        $absentDays = $query->where('status', 'absent')->count();
        $lateDays = $query->where('status', 'late')->count();

        $percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

        return [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'percentage' => $percentage,
        ];
    }

    /**
     * Get attendance defaulters (students with low attendance)
     */
    public function getAttendanceDefaulters(float $thresholdPercentage = 75.0): Collection
    {
        $students = Student::where('status', 'active')
            ->with(['schoolClass'])
            ->get();

        $defaulters = collect();

        foreach ($students as $student) {
            $stats = $this->calculateAttendancePercentage($student->id);

            if ($stats['percentage'] < $thresholdPercentage && $stats['total_days'] > 0) {
                $student->attendance_stats = $stats;
                $defaulters->push($student);
            }
        }

        return $defaulters->sortBy('attendance_stats.percentage');
    }

    /**
     * Get class attendance summary for a date range
     */
    public function getClassAttendanceSummary(int $classId, string $fromDate, string $toDate): array
    {
        $students = Student::where('class_id', $classId)
            ->where('status', 'active')
            ->get();

        $summary = [];

        foreach ($students as $student) {
            $stats = $this->calculateAttendancePercentage($student->id, $fromDate, $toDate);
            $summary[] = [
                'student' => $student,
                'stats' => $stats,
            ];
        }

        return $summary;
    }

    /**
     * Get daily attendance statistics
     */
    public function getDailyAttendanceStats(string $date): array
    {
        $totalStudents = Student::where('status', 'active')->count();

        $attendanceStats = DB::table('attendances')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->whereDate('date', $date)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $presentCount = $attendanceStats['present'] ?? 0;
        $absentCount = $attendanceStats['absent'] ?? 0;
        $lateCount = $attendanceStats['late'] ?? 0;
        $totalMarked = array_sum($attendanceStats);

        return [
            'total_students' => $totalStudents,
            'present_count' => $presentCount,
            'absent_count' => $absentCount,
            'late_count' => $lateCount,
            'total_marked' => $totalMarked,
            'unmarked_count' => $totalStudents - $totalMarked,
            'attendance_percentage' => $totalMarked > 0 ? round(($presentCount / $totalMarked) * 100, 2) : 0,
        ];
    }

    /**
     * Get attendance trends for a period
     */
    public function getAttendanceTrends(string $fromDate, string $toDate): Collection
    {
        return DB::table('attendances')
            ->select(
                DB::raw('DATE(date) as attendance_date'),
                DB::raw('COUNT(CASE WHEN status = "present" THEN 1 END) as present_count'),
                DB::raw('COUNT(CASE WHEN status = "absent" THEN 1 END) as absent_count'),
                DB::raw('COUNT(CASE WHEN status = "late" THEN 1 END) as late_count'),
                DB::raw('COUNT(*) as total_count')
            )
            ->whereBetween('date', [$fromDate, $toDate])
            ->groupBy('attendance_date')
            ->orderBy('attendance_date')
            ->get();
    }
}
