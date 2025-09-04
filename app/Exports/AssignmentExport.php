<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssignmentExport implements FromArray, WithHeadings, WithStyles
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $exportData = [];
        
        foreach ($this->data['assignments'] as $assignment) {
            $exportData[] = [
                $assignment->title,
                $assignment->subject->name,
                $assignment->schoolClass->name,
                $assignment->due_date->format('Y-m-d'),
                $assignment->submission_count ?? 0,
                $assignment->average_grade ?? 'N/A',
                $assignment->status,
                $assignment->teacher->name ?? 'N/A',
            ];
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            'Assignment Title',
            'Subject',
            'Class',
            'Due Date',
            'Submissions',
            'Average Grade',
            'Status',
            'Teacher',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
