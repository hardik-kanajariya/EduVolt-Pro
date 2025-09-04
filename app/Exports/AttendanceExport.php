<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromArray, WithHeadings, WithStyles
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $exportData = [];

        foreach ($this->data['attendance_records'] as $record) {
            $exportData[] = [
                $record->date->format('Y-m-d'),
                $record->student->name,
                $record->schoolClass->name,
                $record->subject->name,
                $record->status,
                $record->time_in ? $record->time_in->format('H:i') : '',
                $record->remarks ?? '',
            ];
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Student Name',
            'Class',
            'Subject',
            'Status',
            'Time In',
            'Remarks',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
