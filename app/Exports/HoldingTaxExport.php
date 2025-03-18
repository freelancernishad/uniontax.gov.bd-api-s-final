<?php
namespace App\Exports;

use App\Models\Holdingtax;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class HoldingTaxExport implements FromCollection, WithHeadings
{
    protected $holdingTaxRecords;

    // Pass filtered records to the constructor
    public function __construct($holdingTaxRecords)
    {
        $this->holdingTaxRecords = $holdingTaxRecords;
    }

    /**
     * Return the collection of HoldingTax data with related HoldingBokeya data.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Return the filtered records passed to the class
        return $this->holdingTaxRecords->map(function ($holdingtax) {
            // Loop through related HoldingBokeya records if needed
            foreach ($holdingtax->holdingBokeyas as $holdingBokeya) {
                $holdingtax->bokeya_year = $holdingBokeya->year;
                $holdingtax->total_bokeya = $holdingBokeya->price;
            }
            return $holdingtax;
        });
    }

    /**
     * Define the headings for the Excel export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Category',
            'Holding No',
            'Maliker Name',
            'Father or Samir Name',
            'Gramer Name',
            'Word No',
            'NID No',
            'Mobile No',
            'Griher Barsikh Mullo',
            'Jomir Vara',
            'Barsikh Vara',
            'Image',
            'Business Name',
            'Bokeya Year',
            'Total Bokeya'
        ];
    }
}

