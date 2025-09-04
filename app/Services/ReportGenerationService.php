<?php

namespace App\Services;

use App\Models\AcademicReport;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Attendance;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportGenerationService
{
    protected StudentProgressService $progressService;

    public function __construct(StudentProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Generate a report based on the academic report configuration
     */
    public function generateReport(AcademicReport $report): bool
    {
        try {
            $report->markAsGenerating();

            $data = $this->collectReportData($report);
            $filePath = $this->exportReport($report, $data);
            $summaryData = $this->generateSummaryData($report, $data);

            $report->markAsCompleted($filePath, $summaryData);

            Log::info('Report generated successfully', [
                'report_id' => $report->id,
                'type' => $report->report_type,
                'file_path' => $filePath,
            ]);

            return true;
        } catch (\Exception $e) {
            $report->markAsFailed($e->getMessage());

            Log::error('Report generation failed', [
                'report_id' => $report->id,
                'type' => $report->report_type,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Collect data for report generation
     */
    protected function collectReportData(AcademicReport $report): array
    {
        return match ($report->report_type) {
            AcademicReport::TYPE_STUDENT_PROGRESS => $this->collectStudentProgressData($report),
            AcademicReport::TYPE_CLASS_PERFORMANCE => $this->collectClassPerformanceData($report),
            AcademicReport::TYPE_ATTENDANCE_SUMMARY => $this->collectAttendanceData($report),
            AcademicReport::TYPE_ASSIGNMENT_ANALYSIS => $this->collectAssignmentData($report),
            AcademicReport::TYPE_EXAM_RESULTS => $this->collectExamData($report),
            AcademicReport::TYPE_BEHAVIORAL_REPORT => $this->collectBehavioralData($report),
            AcademicReport::TYPE_COMPREHENSIVE => $this->collectComprehensiveData($report),
            AcademicReport::TYPE_PARENT_REPORT => $this->collectParentReportData($report),
            AcademicReport::TYPE_TEACHER_REPORT => $this->collectTeacherReportData($report),
            AcademicReport::TYPE_ADMIN_DASHBOARD => $this->collectAdminDashboardData($report),
            default => [],
        };
    }

    /**
     * Student progress report data
     */
    protected function collectStudentProgressData(AcademicReport $report): array
    {
        $query = StudentProgress::query();

        if ($report->student_id) {
            $query->where('student_id', $report->student_id);
        }

        if ($report->academic_year_id) {
            $query->where('academic_year_id', $report->academic_year_id);
        }

        if ($report->class_id) {
            $query->where('class_id', $report->class_id);
        }

        if ($report->subject_id) {
            $query->where('subject_id', $report->subject_id);
        }

        if ($report->term) {
            $query->where('term', $report->term);
        }

        $progressRecords = $query->with([
            'student',
            'academicYear',
            'schoolClass',
            'subject'
        ])->get();

        return [
            'progress_records' => $progressRecords,
            'summary' => [
                'total_records' => $progressRecords->count(),
                'average_grade' => $progressRecords->avg('overall_grade'),
                'pass_rate' => $progressRecords->where('overall_grade', '>=', 50)->count() / max($progressRecords->count(), 1) * 100,
                'attendance_rate' => $progressRecords->avg('attendance_percentage'),
                'top_performers' => $progressRecords->sortByDesc('overall_grade')->take(10),
                'struggling_students' => $progressRecords->where('overall_grade', '<', 50)->sortBy('overall_grade'),
            ],
            'grade_distribution' => $this->calculateGradeDistribution($progressRecords),
            'performance_trends' => $this->calculatePerformanceTrends($progressRecords),
        ];
    }

    /**
     * Class performance report data
     */
    protected function collectClassPerformanceData(AcademicReport $report): array
    {
        $classes = SchoolClass::query();

        if ($report->class_id) {
            $classes->where('id', $report->class_id);
        }

        if ($report->academic_year_id) {
            // Filter by academic year through student progress
            $classes->whereHas('students.progress', function ($query) use ($report) {
                $query->where('academic_year_id', $report->academic_year_id);
            });
        }

        $classData = $classes->with(['students', 'subjects'])->get()->map(function ($class) use ($report) {
            $progressQuery = StudentProgress::where('class_id', $class->id);

            if ($report->academic_year_id) {
                $progressQuery->where('academic_year_id', $report->academic_year_id);
            }

            if ($report->term) {
                $progressQuery->where('term', $report->term);
            }

            $progress = $progressQuery->get();
            $statistics = $this->progressService->getClassStatistics($class->id, $report->academic_year_id ?? 1, $report->term ?? 'first');

            return [
                'class' => $class,
                'statistics' => $statistics,
                'subject_performance' => $this->getSubjectPerformanceByClass($class->id, $report),
            ];
        });

        return [
            'classes' => $classData,
            'overall_statistics' => $this->calculateOverallClassStatistics($classData),
        ];
    }

    /**
     * Attendance summary data
     */
    protected function collectAttendanceData(AcademicReport $report): array
    {
        $query = Attendance::query();

        if ($report->student_id) {
            $query->where('student_id', $report->student_id);
        }

        if ($report->class_id) {
            $query->where('class_id', $report->class_id);
        }

        if ($report->subject_id) {
            $query->where('subject_id', $report->subject_id);
        }

        if ($report->date_from) {
            $query->where('date', '>=', $report->date_from);
        }

        if ($report->date_to) {
            $query->where('date', '<=', $report->date_to);
        }

        $attendanceRecords = $query->with(['student', 'subject', 'schoolClass'])->get();

        return [
            'attendance_records' => $attendanceRecords,
            'summary' => [
                'total_records' => $attendanceRecords->count(),
                'present_count' => $attendanceRecords->where('status', 'present')->count(),
                'absent_count' => $attendanceRecords->where('status', 'absent')->count(),
                'late_count' => $attendanceRecords->where('status', 'late')->count(),
                'attendance_rate' => $attendanceRecords->where('status', 'present')->count() / max($attendanceRecords->count(), 1) * 100,
            ],
            'daily_trends' => $this->calculateDailyAttendanceTrends($attendanceRecords),
            'student_summary' => $this->calculateStudentAttendanceSummary($attendanceRecords),
        ];
    }

    /**
     * Assignment analysis data
     */
    protected function collectAssignmentData(AcademicReport $report): array
    {
        $query = Assignment::query();

        if ($report->class_id) {
            $query->where('class_id', $report->class_id);
        }

        if ($report->subject_id) {
            $query->where('subject_id', $report->subject_id);
        }

        if ($report->date_from) {
            $query->where('due_date', '>=', $report->date_from);
        }

        if ($report->date_to) {
            $query->where('due_date', '<=', $report->date_to);
        }

        $assignments = $query->with(['subject', 'schoolClass', 'submissions'])->get();

        return [
            'assignments' => $assignments,
            'summary' => [
                'total_assignments' => $assignments->count(),
                'total_submissions' => $assignments->sum(fn($a) => $a->submissions->count()),
                'average_submissions_per_assignment' => $assignments->avg(fn($a) => $a->submissions->count()),
                'late_submissions' => $assignments->sum(fn($a) => $a->submissions->where('is_late', true)->count()),
                'average_grade' => $assignments->flatMap->submissions->whereNotNull('grade')->avg('grade'),
            ],
            'assignment_analysis' => $assignments->map(function ($assignment) {
                return [
                    'assignment' => $assignment,
                    'submission_rate' => $this->calculateSubmissionRate($assignment),
                    'grade_statistics' => $this->calculateAssignmentGradeStatistics($assignment),
                ];
            }),
        ];
    }

    /**
     * Exam results data
     */
    protected function collectExamData(AcademicReport $report): array
    {
        $query = Exam::query();

        if ($report->class_id) {
            $query->where('class_id', $report->class_id);
        }

        if ($report->subject_id) {
            $query->where('subject_id', $report->subject_id);
        }

        if ($report->date_from) {
            $query->where('exam_date', '>=', $report->date_from);
        }

        if ($report->date_to) {
            $query->where('exam_date', '<=', $report->date_to);
        }

        $exams = $query->with(['subject', 'schoolClass', 'examMarks.student'])->get();

        return [
            'exams' => $exams,
            'summary' => [
                'total_exams' => $exams->count(),
                'total_students_examined' => $exams->sum(fn($e) => $e->examMarks->count()),
                'average_marks' => $exams->flatMap->examMarks->avg('marks'),
                'pass_rate' => $this->calculateExamPassRate($exams),
            ],
            'exam_analysis' => $exams->map(function ($exam) {
                return [
                    'exam' => $exam,
                    'statistics' => $this->calculateExamStatistics($exam),
                    'grade_distribution' => $this->calculateExamGradeDistribution($exam),
                ];
            }),
        ];
    }

    /**
     * Export report to file
     */
    protected function exportReport(AcademicReport $report, array $data): string
    {
        $filename = $this->generateFilename($report);

        return match ($report->file_format) {
            AcademicReport::FORMAT_PDF => $this->exportToPdf($report, $data, $filename),
            AcademicReport::FORMAT_EXCEL => $this->exportToExcel($report, $data, $filename),
            AcademicReport::FORMAT_CSV => $this->exportToCsv($report, $data, $filename),
            AcademicReport::FORMAT_HTML => $this->exportToHtml($report, $data, $filename),
            AcademicReport::FORMAT_JSON => $this->exportToJson($report, $data, $filename),
            default => $this->exportToPdf($report, $data, $filename),
        };
    }

    /**
     * Export to PDF
     */
    protected function exportToPdf(AcademicReport $report, array $data, string $filename): string
    {
        $view = "reports.{$report->report_type}";

        $pdf = Pdf::loadView($view, [
            'report' => $report,
            'data' => $data,
            'generated_at' => now(),
        ]);

        $filePath = "reports/{$filename}.pdf";
        Storage::put($filePath, $pdf->output());

        return $filePath;
    }

    /**
     * Export to Excel
     */
    protected function exportToExcel(AcademicReport $report, array $data, string $filename): string
    {
        $exportClass = $this->getExcelExportClass($report->report_type);

        $filePath = "reports/{$filename}.xlsx";
        Excel::store(new $exportClass($data), $filePath);

        return $filePath;
    }

    /**
     * Export to CSV
     */
    protected function exportToCsv(AcademicReport $report, array $data, string $filename): string
    {
        $csvData = $this->convertToCsvData($report, $data);

        $filePath = "reports/{$filename}.csv";
        $output = fopen('php://temp', 'w');

        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        Storage::put($filePath, $csvContent);

        return $filePath;
    }

    /**
     * Export to HTML
     */
    protected function exportToHtml(AcademicReport $report, array $data, string $filename): string
    {
        $view = "reports.{$report->report_type}";

        $html = view($view, [
            'report' => $report,
            'data' => $data,
            'generated_at' => now(),
            'format' => 'html',
        ])->render();

        $filePath = "reports/{$filename}.html";
        Storage::put($filePath, $html);

        return $filePath;
    }

    /**
     * Export to JSON
     */
    protected function exportToJson(AcademicReport $report, array $data, string $filename): string
    {
        $jsonData = [
            'report' => $report->toArray(),
            'data' => $data,
            'generated_at' => now()->toISOString(),
        ];

        $filePath = "reports/{$filename}.json";
        Storage::put($filePath, json_encode($jsonData, JSON_PRETTY_PRINT));

        return $filePath;
    }

    /**
     * Generate filename for report
     */
    protected function generateFilename(AcademicReport $report): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $type = str_replace('_', '-', $report->report_type);

        $parts = [$type, $timestamp];

        if ($report->student_id) {
            $parts[] = "student-{$report->student_id}";
        }

        if ($report->class_id) {
            $parts[] = "class-{$report->class_id}";
        }

        if ($report->subject_id) {
            $parts[] = "subject-{$report->subject_id}";
        }

        return implode('_', $parts);
    }

    /**
     * Generate summary data for report
     */
    protected function generateSummaryData(AcademicReport $report, array $data): array
    {
        $summary = [
            'generated_at' => now()->toISOString(),
            'record_count' => 0,
            'data_points' => [],
        ];

        // Add type-specific summary data
        if (isset($data['summary'])) {
            $summary = array_merge($summary, $data['summary']);
        }

        return $summary;
    }

    /**
     * Helper methods for calculations
     */
    protected function calculateGradeDistribution(Collection $progressRecords): array
    {
        $distribution = [];
        $gradeRanges = [
            'A+' => [90, 100],
            'A' => [85, 89],
            'A-' => [80, 84],
            'B+' => [75, 79],
            'B' => [70, 74],
            'B-' => [65, 69],
            'C+' => [60, 64],
            'C' => [55, 59],
            'C-' => [50, 54],
            'D' => [45, 49],
            'F' => [0, 44],
        ];

        foreach ($gradeRanges as $grade => $range) {
            $count = $progressRecords->filter(function ($record) use ($range) {
                return $record->overall_grade >= $range[0] && $record->overall_grade <= $range[1];
            })->count();

            $distribution[$grade] = [
                'count' => $count,
                'percentage' => $progressRecords->count() > 0 ? ($count / $progressRecords->count()) * 100 : 0,
            ];
        }

        return $distribution;
    }

    protected function calculatePerformanceTrends(Collection $progressRecords): array
    {
        return $progressRecords->groupBy('performance_trend')->map(function ($records, $trend) {
            return [
                'trend' => $trend,
                'count' => $records->count(),
                'average_grade' => $records->avg('overall_grade'),
            ];
        })->values()->toArray();
    }

    protected function calculateDailyAttendanceTrends(Collection $attendanceRecords): array
    {
        return $attendanceRecords->groupBy(function ($record) {
            return $record->date->format('Y-m-d');
        })->map(function ($records, $date) {
            $total = $records->count();
            $present = $records->where('status', 'present')->count();

            return [
                'date' => $date,
                'total' => $total,
                'present' => $present,
                'attendance_rate' => $total > 0 ? ($present / $total) * 100 : 0,
            ];
        })->values()->toArray();
    }

    protected function calculateStudentAttendanceSummary(Collection $attendanceRecords): array
    {
        return $attendanceRecords->groupBy('student_id')->map(function ($records, $studentId) {
            $total = $records->count();
            $present = $records->where('status', 'present')->count();
            $absent = $records->where('status', 'absent')->count();
            $late = $records->where('status', 'late')->count();

            return [
                'student' => $records->first()->student,
                'total_classes' => $total,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'attendance_rate' => $total > 0 ? ($present / $total) * 100 : 0,
            ];
        })->values()->toArray();
    }

    // Additional helper methods would go here...
    // This service is quite comprehensive and would continue with more helper methods

    /**
     * Behavioral report data
     */
    protected function collectBehavioralData(AcademicReport $report): array
    {
        $query = StudentProgress::query();

        $this->applyCommonFilters($query, $report);

        $progressRecords = $query->with(['student', 'academicYear', 'schoolClass', 'subject'])->get();

        return [
            'behavioral_data' => $progressRecords->map(function ($record) {
                return [
                    'student' => $record->student,
                    'behavioral_score' => $record->behavioral_score,
                    'effort_level' => $record->effort_level,
                    'participation_level' => $record->participation_level,
                    'achievements' => $record->achievements,
                    'areas_of_concern' => $record->areas_of_concern,
                ];
            }),
            'summary' => [
                'average_behavioral_score' => $progressRecords->avg('behavioral_score'),
                'excellent_behavior_count' => $progressRecords->where('behavioral_score', '>=', 90)->count(),
                'needs_improvement_count' => $progressRecords->where('behavioral_score', '<', 60)->count(),
            ],
        ];
    }

    /**
     * Comprehensive report data
     */
    protected function collectComprehensiveData(AcademicReport $report): array
    {
        return [
            'student_progress' => $this->collectStudentProgressData($report),
            'attendance' => $this->collectAttendanceData($report),
            'assignments' => $this->collectAssignmentData($report),
            'exams' => $this->collectExamData($report),
            'behavioral' => $this->collectBehavioralData($report),
        ];
    }

    /**
     * Parent report data
     */
    protected function collectParentReportData(AcademicReport $report): array
    {
        $studentData = $this->collectStudentProgressData($report);
        $attendanceData = $this->collectAttendanceData($report);

        return [
            'student_overview' => $studentData,
            'attendance_summary' => $attendanceData,
            'upcoming_assignments' => $this->getUpcomingAssignments($report),
            'recent_exams' => $this->getRecentExamResults($report),
            'teacher_comments' => $this->getTeacherComments($report),
        ];
    }

    /**
     * Teacher report data
     */
    protected function collectTeacherReportData(AcademicReport $report): array
    {
        return [
            'class_overview' => $this->collectClassPerformanceData($report),
            'subject_analysis' => $this->getSubjectAnalysis($report),
            'assignment_tracking' => $this->collectAssignmentData($report),
            'attendance_overview' => $this->collectAttendanceData($report),
        ];
    }

    /**
     * Admin dashboard data
     */
    protected function collectAdminDashboardData(AcademicReport $report): array
    {
        return [
            'school_overview' => $this->getSchoolOverview($report),
            'class_performance' => $this->collectClassPerformanceData($report),
            'attendance_trends' => $this->getAttendanceTrends($report),
            'academic_insights' => $this->getAcademicInsights($report),
        ];
    }

    /**
     * Get subject performance by class
     */
    protected function getSubjectPerformanceByClass(int $classId, AcademicReport $report): array
    {
        $query = StudentProgress::where('class_id', $classId);

        if ($report->academic_year_id) {
            $query->where('academic_year_id', $report->academic_year_id);
        }

        if ($report->term) {
            $query->where('term', $report->term);
        }

        return $query->with('subject')
            ->get()
            ->groupBy('subject_id')
            ->map(function ($records, $subjectId) {
                return [
                    'subject' => $records->first()->subject,
                    'average_grade' => $records->avg('overall_grade'),
                    'student_count' => $records->count(),
                    'pass_rate' => $records->where('overall_grade', '>=', 50)->count() / $records->count() * 100,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Calculate overall class statistics
     */
    protected function calculateOverallClassStatistics(Collection $classData): array
    {
        $totalStudents = $classData->sum(fn($class) => $class['statistics']['total_students']);
        $totalClasses = $classData->count();

        return [
            'total_classes' => $totalClasses,
            'total_students' => $totalStudents,
            'average_class_size' => $totalClasses > 0 ? $totalStudents / $totalClasses : 0,
            'overall_average_grade' => $classData->avg(fn($class) => $class['statistics']['average_grade']),
            'overall_pass_rate' => $classData->avg(fn($class) => $class['statistics']['pass_rate']),
            'overall_attendance_rate' => $classData->avg(fn($class) => $class['statistics']['attendance_rate']),
        ];
    }

    /**
     * Calculate submission rate for assignment
     */
    protected function calculateSubmissionRate(Assignment $assignment): float
    {
        $expectedSubmissions = $assignment->schoolClass->students()->count();
        $actualSubmissions = $assignment->submissions()->count();

        return $expectedSubmissions > 0 ? ($actualSubmissions / $expectedSubmissions) * 100 : 0;
    }

    /**
     * Calculate assignment grade statistics
     */
    protected function calculateAssignmentGradeStatistics(Assignment $assignment): array
    {
        $submissions = $assignment->submissions()->whereNotNull('grade')->get();

        if ($submissions->isEmpty()) {
            return [
                'average' => 0,
                'median' => 0,
                'highest' => 0,
                'lowest' => 0,
                'pass_rate' => 0,
            ];
        }

        $grades = $submissions->pluck('grade')->sort();

        return [
            'average' => $grades->avg(),
            'median' => $grades->median(),
            'highest' => $grades->max(),
            'lowest' => $grades->min(),
            'pass_rate' => $grades->where('>=', 50)->count() / $grades->count() * 100,
        ];
    }

    /**
     * Calculate exam pass rate
     */
    protected function calculateExamPassRate(Collection $exams): float
    {
        $totalStudents = $exams->sum(fn($exam) => $exam->examMarks->count());
        $passedStudents = $exams->sum(fn($exam) => $exam->examMarks->where('marks', '>=', 50)->count());

        return $totalStudents > 0 ? ($passedStudents / $totalStudents) * 100 : 0;
    }

    /**
     * Calculate exam statistics
     */
    protected function calculateExamStatistics(Exam $exam): array
    {
        $marks = $exam->examMarks->pluck('marks');

        if ($marks->isEmpty()) {
            return [
                'average' => 0,
                'median' => 0,
                'highest' => 0,
                'lowest' => 0,
                'pass_rate' => 0,
                'student_count' => 0,
            ];
        }

        return [
            'average' => $marks->avg(),
            'median' => $marks->median(),
            'highest' => $marks->max(),
            'lowest' => $marks->min(),
            'pass_rate' => $marks->where('>=', 50)->count() / $marks->count() * 100,
            'student_count' => $marks->count(),
        ];
    }

    /**
     * Calculate exam grade distribution
     */
    protected function calculateExamGradeDistribution(Exam $exam): array
    {
        $marks = $exam->examMarks->pluck('marks');
        $gradeRanges = [
            'A+' => [90, 100],
            'A' => [85, 89],
            'A-' => [80, 84],
            'B+' => [75, 79],
            'B' => [70, 74],
            'B-' => [65, 69],
            'C+' => [60, 64],
            'C' => [55, 59],
            'C-' => [50, 54],
            'D' => [45, 49],
            'F' => [0, 44],
        ];

        $distribution = [];
        foreach ($gradeRanges as $grade => $range) {
            $count = $marks->filter(fn($mark) => $mark >= $range[0] && $mark <= $range[1])->count();
            $distribution[$grade] = [
                'count' => $count,
                'percentage' => $marks->count() > 0 ? ($count / $marks->count()) * 100 : 0,
            ];
        }

        return $distribution;
    }

    /**
     * Get Excel export class
     */
    protected function getExcelExportClass(string $reportType): string
    {
        return match ($reportType) {
            AcademicReport::TYPE_STUDENT_PROGRESS => App\Exports\StudentProgressExport::class,
            AcademicReport::TYPE_CLASS_PERFORMANCE => App\Exports\ClassPerformanceExport::class,
            AcademicReport::TYPE_ATTENDANCE_SUMMARY => App\Exports\AttendanceExport::class,
            AcademicReport::TYPE_ASSIGNMENT_ANALYSIS => App\Exports\AssignmentExport::class,
            AcademicReport::TYPE_EXAM_RESULTS => App\Exports\ExamResultsExport::class,
            default => App\Exports\GenericReportExport::class,
        };
    }

    /**
     * Convert data to CSV format
     */
    protected function convertToCsvData(AcademicReport $report, array $data): array
    {
        $csvData = [];

        // Add headers based on report type
        switch ($report->report_type) {
            case AcademicReport::TYPE_STUDENT_PROGRESS:
                $csvData[] = ['Student Name', 'Subject', 'Class', 'Overall Grade', 'Letter Grade', 'GPA', 'Attendance %'];
                foreach ($data['progress_records'] as $record) {
                    $csvData[] = [
                        $record->student->name,
                        $record->subject->name,
                        $record->schoolClass->name,
                        $record->overall_grade,
                        $record->letter_grade,
                        $record->gpa,
                        $record->attendance_percentage,
                    ];
                }
                break;

            case AcademicReport::TYPE_ATTENDANCE_SUMMARY:
                $csvData[] = ['Date', 'Student', 'Subject', 'Class', 'Status'];
                foreach ($data['attendance_records'] as $record) {
                    $csvData[] = [
                        $record->date->format('Y-m-d'),
                        $record->student->name,
                        $record->subject->name,
                        $record->schoolClass->name,
                        $record->status,
                    ];
                }
                break;

            default:
                $csvData[] = ['Report Type', 'Generated At', 'Summary'];
                $csvData[] = [$report->report_type, now()->format('Y-m-d H:i:s'), json_encode($data['summary'] ?? [])];
        }

        return $csvData;
    }

    /**
     * Apply common filters to query
     */
    protected function applyCommonFilters($query, AcademicReport $report): void
    {
        if ($report->student_id) {
            $query->where('student_id', $report->student_id);
        }

        if ($report->academic_year_id) {
            $query->where('academic_year_id', $report->academic_year_id);
        }

        if ($report->class_id) {
            $query->where('class_id', $report->class_id);
        }

        if ($report->subject_id) {
            $query->where('subject_id', $report->subject_id);
        }

        if ($report->term) {
            $query->where('term', $report->term);
        }
    }

    /**
     * Additional helper methods for advanced reports
     */
    protected function getUpcomingAssignments(AcademicReport $report): array
    {
        $query = Assignment::where('due_date', '>', now());

        if ($report->class_id) {
            $query->where('class_id', $report->class_id);
        }

        return $query->with(['subject', 'schoolClass'])
            ->orderBy('due_date')
            ->limit(10)
            ->get()
            ->toArray();
    }

    protected function getRecentExamResults(AcademicReport $report): array
    {
        $query = Exam::where('exam_date', '<=', now())
            ->where('exam_date', '>=', now()->subDays(30));

        if ($report->class_id) {
            $query->where('class_id', $report->class_id);
        }

        return $query->with(['subject', 'examMarks'])
            ->orderBy('exam_date', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    protected function getTeacherComments(AcademicReport $report): array
    {
        $query = StudentProgress::whereNotNull('teacher_comments');

        $this->applyCommonFilters($query, $report);

        return $query->with(['student', 'subject'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($record) => [
                'student' => $record->student->name,
                'subject' => $record->subject->name,
                'comment' => $record->teacher_comments,
                'date' => $record->updated_at->format('Y-m-d'),
            ])
            ->toArray();
    }

    protected function getSubjectAnalysis(AcademicReport $report): array
    {
        // Implementation for subject-specific analysis
        return [];
    }

    protected function getSchoolOverview(AcademicReport $report): array
    {
        // Implementation for school-wide overview
        return [
            'total_students' => Student::count(),
            'total_classes' => SchoolClass::count(),
            'total_subjects' => Subject::count(),
            'current_academic_year' => AcademicYear::where('is_current', true)->first(),
        ];
    }

    protected function getAttendanceTrends(AcademicReport $report): array
    {
        // Implementation for attendance trend analysis
        return [];
    }

    protected function getAcademicInsights(AcademicReport $report): array
    {
        // Implementation for academic insights
        return [];
    }
}
