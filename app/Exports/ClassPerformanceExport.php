<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClassPerformanceExport implements FromArray, WithHeadings, WithStyles
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $exportData = [];

        foreach ($this->data['classes'] as $classData) {
            $class = $classData['class'];
            $stats = $classData['statistics'];

            $exportData[] = [
                $class->name,
                $stats['total_students'],
                $stats['average_grade'],
                $stats['pass_rate'],
                $stats['attendance_rate'],
            ];
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            'Class Name',
            'Total Students',
            'Average Grade',
            'Pass Rate (%)',
            'Attendance Rate (%)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
