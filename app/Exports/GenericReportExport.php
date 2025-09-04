<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericReportExport implements FromArray, WithHeadings, WithStyles
{
    protected array $data;
    protected array $headers;

    public function __construct(array $data, array $headers = [])
    {
        $this->data = $data;
        $this->headers = $headers;
    }

    public function array(): array
    {
        if (isset($this->data['report_data']) && is_array($this->data['report_data'])) {
            return $this->data['report_data'];
        }
        
        // If the data structure is different, try to flatten it
        $exportData = [];
        foreach ($this->data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    if (is_array($item)) {
                        $exportData[] = array_values($item);
                    } elseif (is_object($item)) {
                        $exportData[] = array_values((array) $item);
                    } else {
                        $exportData[] = [$item];
                    }
                }
            }
        }
        
        return $exportData;
    }

    public function headings(): array
    {
        if (!empty($this->headers)) {
            return $this->headers;
        }
        
        return [
            'Data',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
