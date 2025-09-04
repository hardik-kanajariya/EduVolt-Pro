<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentProgressExport implements FromArray, WithHeadings, WithStyles
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $exportData = [];
        
        foreach ($this->data['progress_records'] as $record) {
            $exportData[] = [
                $record->student->name,
                $record->academicYear->year,
                $record->schoolClass->name,
                $record->subject->name,
                $record->term,
                $record->overall_grade,
                $record->letter_grade,
                $record->gpa,
                $record->attendance_percentage,
                $record->assignment_average,
                $record->exam_average,
                $record->total_assignments,
                $record->submitted_assignments,
                $record->total_exams,
                $record->exams_taken,
                $record->performance_trend,
                $record->behavioral_score,
                $record->effort_level,
                $record->participation_level,
            ];
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Academic Year',
            'Class',
            'Subject',
            'Term',
            'Overall Grade',
            'Letter Grade',
            'GPA',
            'Attendance %',
            'Assignment Average',
            'Exam Average',
            'Total Assignments',
            'Submitted Assignments',
            'Total Exams',
            'Exams Taken',
            'Performance Trend',
            'Behavioral Score',
            'Effort Level',
            'Participation Level',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
