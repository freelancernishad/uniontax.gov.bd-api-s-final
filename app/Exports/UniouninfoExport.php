<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UniouninfoExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnWidths, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Return the collection of data
     */
    public function collection()
    {
        return collect($this->data);
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'Merchant ID', 
            'Organization', 
            'Server IP', 
            'Mobile', 
            'Password', 
            'URL'
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25, // Merchant ID
            'B' => 40, // Organization
            'C' => 20, // Server IP
            'D' => 15, // Mobile
            'E' => 15, // Password
            'F' => 40, // URL
        ];
    }

    /**
     * Style the spreadsheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFFF00']]], // Yellow Header
            'A1:F1' => ['alignment' => ['horizontal' => 'center']], // Center align headers
        ];
    }
}
