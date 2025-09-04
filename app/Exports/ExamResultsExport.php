<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExamResultsExport implements FromArray, WithHeadings, WithStyles
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $exportData = [];
        
        foreach ($this->data['exam_results'] as $result) {
            $exportData[] = [
                $result->student->name,
                $result->examination->name,
                $result->subject->name,
                $result->obtained_marks,
                $result->total_marks,
                $result->percentage,
                $result->grade,
                $result->status,
            ];
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Examination',
            'Subject',
            'Obtained Marks',
            'Total Marks',
            'Percentage',
            'Grade',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
