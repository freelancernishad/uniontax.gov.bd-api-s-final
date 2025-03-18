<?php

namespace App\Imports;

use App\Models\Holdingtax;
use App\Models\HoldingBokeya;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class HoldingTaxImport implements ToModel, WithHeadingRow
{
    protected $unioun;
    protected $holdingCache = [];
    protected $importedData;  // Store the imported data

    public function __construct($unioun, &$importedData)
    {
        $this->unioun = $unioun;
        $this->importedData = &$importedData;  // Pass by reference
    }

    public function model(array $row)
    {
        // Validate the row data
        $validator = Validator::make($row, [
            'category' => 'required',
            'holding_no' => 'required',
            'maliker_name' => 'required',
            'father_or_samir_name' => 'required',
            'gramer_name' => 'required',
            'word_no' => 'required',
            'nid_no' => 'required',
            'mobile_no' => 'required',
            'griher_barsikh_mullo' => 'nullable|numeric',
            'jomir_vara' => 'nullable|numeric',
            'barsikh_vara' => 'nullable|numeric',
            'image' => 'nullable',
            'busnessName' => 'nullable',
            'bokeya_year' => 'nullable', // Ensuring it's a string
            'bokeya_price' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            \Log::error("Validation failed", [
                'errors' => $validator->errors(),
                'row' => $row
            ]);
            return null; // Skip invalid rows instead of stopping the entire import
        }

        // Ensure all necessary keys exist in the row and are not null
        $requiredKeys = ['category', 'holding_no', 'griher_barsikh_mullo', 'jomir_vara', 'barsikh_vara'];
        $missingKeys = [];

        // Set default values if the keys are missing or null
        $row['griher_barsikh_mullo'] = $row['griher_barsikh_mullo'] ?? 0;
        $row['jomir_vara'] = $row['jomir_vara'] ?? 0;
        $row['barsikh_vara'] = $row['barsikh_vara'] ?? 0;

        foreach ($requiredKeys as $key) {
            if (!isset($row[$key]) || is_null($row[$key])) {
                $missingKeys[] = $key;
            }
        }

        if (!empty($missingKeys)) {
            \Log::error("Missing required data", [
                'row' => $row,
                'missingKeys' => $missingKeys
            ]);
            return null; // Skip rows with missing data
        }

        // Check if Holdingtax already exists for this holding_no
        if (!isset($this->holdingCache[$row['holding_no']])) {
            $calculationResults = $this->calculateHoldingTax(
                $row['category'],
                $row['griher_barsikh_mullo'],
                $row['jomir_vara'],
                $row['barsikh_vara']
            );

            $currentYearKor = $calculationResults['current_year_kor'];
            $data = array_merge($row, $calculationResults);
            $data['unioun'] = $this->unioun;
            $data['total_bokeya'] = $currentYearKor;

            $holding = Holdingtax::create($data);
            $this->holdingCache[$row['holding_no']] = $holding->id;

            $this->createHoldingBokeya($holding->id, CurrentOrthoBochor(1), $currentYearKor);
        } else {
            $holding = Holdingtax::find($this->holdingCache[$row['holding_no']]);
        }

        if (!empty($row['bokeya_year']) && !empty($row['bokeya_price'])) {
            $this->createHoldingBokeya($holding->id, $row['bokeya_year'], $row['bokeya_price']);
        }


        // if ($holding) {

        //     $this->importedData[] = $holding;
        // }


        return $holding;
    }






    private function calculateHoldingTax($category, $griherBarsikhMullo, $jomirVara, $barsikhVara = null)
    {


            switch ($category) {
                case 'মালিক নিজে বসবাসকারী':
                    return $this->calculateOwnerTax($griherBarsikhMullo, $jomirVara);
                case 'প্রতিষ্ঠান':
                    return $this->calculateInstitutionTax($griherBarsikhMullo, $jomirVara);
                case 'ভাড়া':
                    return $this->calculateRentTax($barsikhVara);
                case 'আংশিক ভাড়া':
                    return $this->calculatePartialRentTax($griherBarsikhMullo, $jomirVara, $barsikhVara);
                default:
                    // throw new \Exception("Invalid category provided.");
                    return response()->json([
                        'message' => "Invalid category provided: '{$category}'.",
                        'valid_categories' => [
                            'মালিক নিজে বসবাসকারী',
                            'প্রতিষ্ঠান',
                            'ভাড়া',
                            'আংশিক ভাড়া'
                        ]
                    ], 400);
            }

    }

    private function calculateOwnerTax($griherBarsikhMullo, $jomirVara)
    {
        $barsikhMullerPercent = ($griherBarsikhMullo * 7.5) / 100;
        $totalMullo = $jomirVara + $barsikhMullerPercent;
        $rokhonaBekhonKhoroch = $totalMullo / 6;
        $prakklitoMullo = $totalMullo - $rokhonaBekhonKhoroch;
        $reyad = $prakklitoMullo / 4;
        $prodeyKorjoggoBarsikhMullo = $prakklitoMullo - $reyad;
        $currentYearKor = ($prodeyKorjoggoBarsikhMullo * 7) / 100;

        if ($currentYearKor >= 500) {
            $currentYearKor = 500;
        }

        return [
            'barsikh_muller_percent' => $barsikhMullerPercent,
            'rokhona_bekhon_khoroch_percent' => 0,
            'total_mullo' => $totalMullo,
            'rokhona_bekhon_khoroch' => $rokhonaBekhonKhoroch,
            'prakklito_mullo' => $prakklitoMullo,
            'reyad' => $reyad,
            'prodey_korjoggo_barsikh_mullo' => $prodeyKorjoggoBarsikhMullo,
            'prodey_korjoggo_barsikh_varar_mullo' => 0,
            'angsikh_prodoy_korjoggo_barsikh_mullo' => 0,
            'current_year_kor' => $currentYearKor,
            'total_prodey_korjoggo_barsikh_mullo' => 0,
        ];
    }

    private function calculateInstitutionTax($griherBarsikhMullo, $jomirVara)
    {
        $barsikhMullerPercent = ($griherBarsikhMullo * 7.5) / 100;
        $totalMullo = $jomirVara + $barsikhMullerPercent;
        $rokhonaBekhonKhoroch = $totalMullo / 6;
        $prakklitoMullo = $totalMullo - $rokhonaBekhonKhoroch;
        $reyad = $prakklitoMullo / 4;
        $prodeyKorjoggoBarsikhMullo = $prakklitoMullo - $reyad;
        $currentYearKor = ($prodeyKorjoggoBarsikhMullo * 7) / 100;

        return [
            'barsikh_muller_percent' => $barsikhMullerPercent,
            'rokhona_bekhon_khoroch_percent' => 0,
            'total_mullo' => $totalMullo,
            'rokhona_bekhon_khoroch' => $rokhonaBekhonKhoroch,
            'prakklito_mullo' => $prakklitoMullo,
            'reyad' => $reyad,
            'prodey_korjoggo_barsikh_mullo' => $prodeyKorjoggoBarsikhMullo,
            'prodey_korjoggo_barsikh_varar_mullo' => 0,
            'angsikh_prodoy_korjoggo_barsikh_mullo' => 0,
            'current_year_kor' => $currentYearKor,
            'total_prodey_korjoggo_barsikh_mullo' => 0,
        ];
    }

    private function calculateRentTax($barsikhVara)
    {
        $rokhonaBekhonKhorochPercent = $barsikhVara / 6;
        $prodeyKorjoggoBarsikhVararMullo = $barsikhVara - $rokhonaBekhonKhorochPercent;
        $currentYearKor = ($prodeyKorjoggoBarsikhVararMullo * 7) / 100;

        if ($currentYearKor >= 500) {
            $currentYearKor = 500;
        }

        return [
            'barsikh_muller_percent' => 0,
            'total_mullo' => $barsikhVara,
            'rokhona_bekhon_khoroch_percent' => $rokhonaBekhonKhorochPercent,
            'prodey_korjoggo_barsikh_varar_mullo' => $prodeyKorjoggoBarsikhVararMullo,
            'angsikh_prodoy_korjoggo_barsikh_mullo' => 0,
            'current_year_kor' => $currentYearKor,
            'total_prodey_korjoggo_barsikh_mullo' => 0,
        ];
    }

    private function calculatePartialRentTax($griherBarsikhMullo, $jomirVara, $barsikhVara)
    {
        $barsikhMullerPercent = ($griherBarsikhMullo * 7.5) / 100;
        $totalMullo = $jomirVara + $barsikhMullerPercent;
        $rokhonaBekhonKhoroch = $totalMullo / 6;
        $prakklitoMullo = $totalMullo - $rokhonaBekhonKhoroch;
        $reyad = $prakklitoMullo / 4;
        $angsikhProdoyKorjoggoBarsikhMullo = $prakklitoMullo - $reyad;
        $rokhonaBekhonKhorochPercent = $barsikhVara / 6;
        $prodeyKorjoggoBarsikhVararMullo = $barsikhVara - $rokhonaBekhonKhorochPercent;
        $totalProdeyKorjoggoBarsikhMullo = $angsikhProdoyKorjoggoBarsikhMullo + $prodeyKorjoggoBarsikhVararMullo;
        $currentYearKor = ($totalProdeyKorjoggoBarsikhMullo * 7) / 100;

        return [
            'barsikh_muller_percent' => $barsikhMullerPercent,
            'total_mullo' => $totalMullo,
            'rokhona_bekhon_khoroch' => $rokhonaBekhonKhoroch,
            'prakklito_mullo' => $prakklitoMullo,
            'reyad' => $reyad,
            'angsikh_prodoy_korjoggo_barsikh_mullo' => $angsikhProdoyKorjoggoBarsikhMullo,
            'rokhona_bekhon_khoroch_percent' => $rokhonaBekhonKhorochPercent,
            'prodey_korjoggo_barsikh_varar_mullo' => $prodeyKorjoggoBarsikhVararMullo,
            'current_year_kor' => $currentYearKor,
            'total_prodey_korjoggo_barsikh_mullo' => 0,
        ];
    }








    private function createHoldingBokeya($holdingTaxId, $year, $price)
    {
        HoldingBokeya::create([
            'holdingTax_id' => $holdingTaxId,
            'year' => $year,
            'price' => $price,
            'status' => 'Unpaid',
        ]);
    }




}
