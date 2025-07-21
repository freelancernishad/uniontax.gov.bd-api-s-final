<?php

namespace App\Services;

use Exception;
use App\Models\Sonod;
use App\Models\SonodFee;
use App\Models\Uniouninfo;
use Illuminate\Support\Arr;
use App\Models\Sonodnamelist;
use App\Models\SonodHoldingOwner;
use App\Models\TradeLicenseKhatFee;

class SonodCreatorService
{
    public function create($bnData, $enData, $holdingData, $request)
    {
        if (empty($bnData)) {
            throw new Exception('বাংলা ডাটা পাওয়া যায়নি');
        }

        // Process successor_list
        $successor_list = json_encode($bnData['successor_list'] ?? []);
        $enSuccessorList = json_encode($enData['successor_list'] ?? []);

        // Get Sonod English name
        $sonodName = $bnData['sonod_name'];
        $sonodEnName = Sonodnamelist::where('bnname', $sonodName)->first();
        if (!$sonodEnName) {
            throw new Exception('No data found for the given Sonod name.');
        }

        $filePath = str_replace(' ', '_', $sonodEnName->enname);
        $dateFolder = date("Y/m/d");

        // Generate uniqueKey
        $unionName = $bnData['unioun_name'];
        do {
            $uniqueKey = md5(uniqid($unionName . $sonodName . microtime(), true));
            $existingSonod = Sonod::where('uniqeKey', $uniqueKey)->first();
        } while ($existingSonod);








        $insertData = array_merge($bnData, [
            'applicant_type_of_businessKhat' => $bnData['applicant_type_of_businessKhat'] ?? null,
            'applicant_type_of_businessKhatAmount' => $bnData['applicant_type_of_businessKhatAmount'] ?? 0,
            'organization_word_no' => $bnData['organization_word_no'] ?? null,
            'uniqeKey' => $uniqueKey,
            'khat' => "সনদ ফি",
            'stutus' => "Pepaid",
            'payment_status' => "Unpaid",
            'year' => date('Y'),
            'hasEnData' => !empty($enData),
        ]);


        $sonodId =  (string) sonodId($unionName, $sonodName, getOrthoBchorYear());
        $insertData['sonod_Id'] =  $sonodId;


        $insertData = array_merge($insertData, $this->prepareSonodData($request, $sonodName, $successor_list, $unionName, $sonodId));

        handleFileUploads($request, $insertData, $filePath, $dateFolder, $sonodId);

        if (isset($bnData['Annual_income'])) {
            $insertData['Annual_income'] = $bnData['Annual_income'];
            $insertData['Annual_income_text'] = convertAnnualIncomeToText($bnData['Annual_income']);
        }

        // Handle charges
        $this->handleCharges($bnData, $enData, $sonodEnName, $insertData);

        if (isset($bnData['image']) && $bnData['image']) {
            $insertData['image'] = uploadBase64Image($bnData['image'], $filePath, $dateFolder, $sonodId);
        }

        if (!empty($bnData['applicant_date_of_birth'])) {
            $insertData['applicant_date_of_birth'] = convertToMySQLDate(int_bn_to_en($bnData['applicant_date_of_birth']));
        }

        $sonodData = Arr::except($insertData, ['holding_owner_name', 'holding_owner_mobile', 'holding_owner_relationship']);

        $sonod = Sonod::create($sonodData);

        handleSonodFileUploads($request, $filePath, $dateFolder, $sonod->id);

        // Holding Owner store
        if (
            !empty($insertData['holding_owner_name']) &&
            !empty($insertData['holding_owner_mobile']) &&
            !empty($insertData['holding_owner_relationship'])
        ) {
            $holdingData['sonod_id'] = $sonod->id;
            $holdingData['holding_no'] = $insertData['applicant_holding_tax_number'] ?? null;
            $holdingData['name'] = $insertData['holding_owner_name'];
            $holdingData['mobile'] = $insertData['holding_owner_mobile'];
            $holdingData['relationship'] = $insertData['holding_owner_relationship'];

            SonodHoldingOwner::create($holdingData);
        }

        return $sonod;
    }




    private function handleCharges($bnData, $enData, $sonodnamelist, &$insertData)
    {
        $tradeVat = 15;

        // Fetch last_years_money from bnData or enData
        $lastYearsMoney = $bnData['last_years_money'] ?? $enData['last_years_money'] ?? 0;

        // Fetch sonod_name and unioun_name from bnData or enData
        $sonodName = $bnData['sonod_name'] ?? $enData['sonod_name'] ?? null;
        $uniounName = $bnData['unioun_name'] ?? $enData['unioun_name'] ?? null;

        if (!$sonodName || !$uniounName) {
            throw new Exception('Sonod name or union name is missing.');
        }

        // Fetch the corresponding sonod fee from the SonodFee table
        $sonodFeeRecord = SonodFee::where([
            'service_id' => $sonodnamelist->service_id,
            'unioun' => $uniounName
        ])->first();

        if (!$sonodFeeRecord) {
            throw new Exception('Sonod fee not found.');
        }

        $sonodFee = $sonodFeeRecord->fees;
        $signboard_fee = 0;
        // Check if it's a 'ট্রেড লাইসেন্স' and retrieve the PesaKor fee
        if ($sonodName == 'ট্রেড লাইসেন্স') {
            $khat_id_1 = $bnData['applicant_type_of_businessKhat'] ?? $enData['applicant_type_of_businessKhat'] ?? null;
            $khat_id_2 = $bnData['applicant_type_of_businessKhatAmount'] ?? $enData['applicant_type_of_businessKhatAmount'] ?? 0;

            $pesaKorFee = TradeLicenseKhatFee::where([
                'khat_id_1' => $khat_id_1,
                'khat_id_2' => $khat_id_2
            ])->first();

            $pesaKor = $pesaKorFee ? $pesaKorFee->fee : 0;


            $isUnion = isUnion();
            if($isUnion){
                $tradeVatAmount = ($sonodFee * $tradeVat) / 100;
            }else{
                // $tradeVatAmount = ($pesaKor * $tradeVat) / 100;
                $tradeVatAmount = 0;


                $signboard_type = $bnData['signboard_type'] ?? $enData['signboard_type'] ?? 'normal';
                $signboard_size_square_fit = $bnData['signboard_size_square_fit'] ?? $enData['signboard_size_square_fit'] ?? 0;
                $signboard_size_square_fit = (float) $signboard_size_square_fit; // Ensure numeric value

                $signboard_fee = 0;
                if ($signboard_type == 'normal') {
                    $signboard_fee = $signboard_size_square_fit * 100;
                } elseif ($signboard_type == 'digital_led') {
                    $signboard_fee = $signboard_size_square_fit * 150;
                }

            }
        } else {
            $pesaKor = 0;
            $tradeVatAmount = 0;
            $signboard_fee = 0;
        }

        // Calculate total amount and currently paid money
        $totalAmount = $sonodFee + $tradeVatAmount + $pesaKor + $signboard_fee;



        $currentlyPaidMoney = (int)$totalAmount;

        // Prepare amount details for JSON encoding
        $amountDetails = json_encode([
            'total_amount' => $currentlyPaidMoney + (int)$lastYearsMoney,
            'pesaKor' => (string)$pesaKor,
            'tredeLisenceFee' => (string)$sonodFee,
            'vatAykor' => (string)$tradeVat,
            'khat' => null,
            'signboard_fee' => (int)$signboard_fee,
            'last_years_money' => (string)$lastYearsMoney,
            'currently_paid_money' => (string)$currentlyPaidMoney
        ]);

        // Update insertData with calculated values
        $insertData['last_years_money'] = $lastYearsMoney;
        $insertData['currently_paid_money'] = $currentlyPaidMoney;
        $insertData['total_amount'] = $currentlyPaidMoney + (int)$lastYearsMoney;
        $insertData['the_amount_of_money_in_words'] = convertAnnualIncomeToText($currentlyPaidMoney + (int)$lastYearsMoney);
        $insertData['amount_deails'] = $amountDetails;
    }


     private function prepareSonodData($request, $sonodName, $successor_list, $unionName, $sonodId)
    {
        $insertData = [];

        // Specific adjustments based on sonod name
        if ($sonodName == 'একই নামের প্রত্যয়ন' || $sonodName == 'বিবিধ প্রত্যয়নপত্র') {
            $insertData['sameNameNew'] = 1;
        }

        // Set the orthoBchor based on current year/month
        $insertData['orthoBchor'] = getOrthoBchorYear();

        // Set additional fields from the union info
        $unionInfo = Uniouninfo::where('short_name_e', $unionName)->latest()->first();
        $insertData['chaireman_name'] = $unionInfo->c_name;
        $insertData['c_email'] = $unionInfo->c_email;
        $insertData['chaireman_sign'] = $unionInfo->c_signture;
        $insertData['chaireman_type'] = $unionInfo->c_type;

        // Add successor list
        $insertData['successor_list'] = $successor_list;

        // Set union chairman and secretary info
        $insertData['socib_name'] = $unionInfo->socib_name;
        $insertData['socib_email'] = $unionInfo->socib_email;
        $insertData['socib_signture'] = $unionInfo->socib_signture;
        $insertData['sonod_Id'] = $sonodId;

        return $insertData;
    }




}
