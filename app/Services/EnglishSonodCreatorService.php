<?php

namespace App\Services;

use Exception;
use App\Models\Sonod;

use App\Models\EnglishSonod;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use Devfaysal\BangladeshGeocode\Models\District;

class EnglishSonodCreatorService
{
    public function createOrUpdate($sonod, $enData, $applicationfor="both", $request)
    {
        if (empty($enData)) {
            throw new Exception("English data is required.");
        }

        if (!$sonod) {
            throw new Exception("Sonod object is required.");
        }

        // Generate uniqeKey
        $unionName = $sonod->unioun_name;
        $sonodName = $sonod->sonod_name;
        $uniqueKey = md5(uniqid($unionName . $sonodName . microtime(), true));

        // Successor list
        $enSuccessorList = json_encode($enData['successor_list'] ?? []);

        // Build English Sonod data
        $englishSonodData = array_merge($enData, [
            'applicant_gender' => $sonod->applicant_gender == 'পুরুষ' ? 'Male' : ($sonod->applicant_gender == 'মহিলা' ? 'Female' : ''),
            'applicant_national_id_number' => int_bn_to_en($sonod->applicant_national_id_number),
            'applicant_birth_certificate_number' => int_bn_to_en($sonod->applicant_birth_certificate_number),
            'applicant_holding_tax_number' => int_bn_to_en($sonod->applicant_holding_tax_number),
            'applicant_mobile' => int_bn_to_en($sonod->applicant_mobile),
            'applicant_date_of_birth' => convertToMySQLDate(int_bn_to_en($sonod->applicant_date_of_birth)),
            'image' => $sonod->image,
            'applicant_owner_type' => $this->convertOwnership($sonod->applicant_owner_type),
            'applicant_vat_id_number' => $sonod->applicant_vat_id_number,
            'applicant_tax_id_number' => $sonod->applicant_tax_id_number,
            'applicant_type_of_businessKhat' => $sonod->applicant_type_of_businessKhat,
            'applicant_type_of_businessKhatAmount' => $sonod->applicant_type_of_businessKhatAmount,
            'last_years_money' => $sonod->last_years_money,
            'orthoBchor' => int_bn_to_en($sonod->orthoBchor),
            'applicant_email' => $sonod->applicant_email,
            'applicant_resident_status' => $sonod->applicant_resident_status == 'স্থায়ী' ? 'Permanent' : ($sonod->applicant_resident_status == 'অস্থায়ী' ? 'Temporary' : ''),

            'applicant_present_district' => optional(District::where('bn_name', $sonod->applicant_present_district)->first())->name,
            'applicant_present_Upazila' => optional(Upazila::where('bn_name', $sonod->applicant_present_Upazila)->first())->name,
            'applicant_present_word_number' => int_bn_to_en($sonod->applicant_present_word_number),

            'applicant_permanent_district' => optional(District::where('bn_name', $sonod->applicant_permanent_district)->first())->name,
            'applicant_permanent_Upazila' => optional(Upazila::where('bn_name', $sonod->applicant_permanent_Upazila)->first())->name,
            'applicant_permanent_word_number' => int_bn_to_en($sonod->applicant_permanent_word_number),

            'organization_word_no' => $sonod->organization_word_no ?? null,

            'alive_status' => $sonod->alive_status ?? 0,
            'format' => $sonod->format,
            'sonod_Id' => $sonod->id,
            'uniqeKey' => $uniqueKey,
            'khat' => "সনদ ফি",
            'stutus' => "Pepaid",
            'payment_status' => "Unpaid",
            'year' => date('Y'),
            'successor_list' => $enSuccessorList,
        ]);

        // Create or Update EnglishSonod
        $existingEnglishSonod = EnglishSonod::where('sonod_Id', $sonod->id)->first();

        if ($existingEnglishSonod) {
            $existingEnglishSonod->update($englishSonodData);
        } else {
            EnglishSonod::create($englishSonodData);
        }

        // Double the price if both Sonod and EnglishSonod exist
        if ($applicationfor == "both") {
            $this->doublePriceForBoth($sonod);
        }
        // $this->doublePriceForBoth($sonod);

        return true;
    }

    private function convertOwnership($type)
    {
        return match ($type) {
            'ব্যক্তি মালিকানাধীন' => 'Individual Ownership',
            'যৌথ মালিকানা' => 'Joint Ownership',
            'কোম্পানী' => 'Company',
            default => '',
        };
    }

    private function doublePriceForBoth($sonod)
    {
        // Decode the amount_details JSON
        $amountDetails = json_decode($sonod->amount_deails, true);

        // Check if the sonod_name is 'ট্রেড লাইসেন্স'
        if ($sonod->sonod_name == 'ট্রেড লাইসেন্স') {


            $isUnion = isUnion();
            if($isUnion){
                            // Get the trade license fee and calculate 15% VAT
                $tredeLisenceFee = (float)$amountDetails['tredeLisenceFee'];
                $vatAykor = $tredeLisenceFee * 0.15; // 15% VAT

                // Add the trade license fee and VAT to the total amount
                $amountDetails['total_amount'] = (string)((float)$amountDetails['total_amount'] + $tredeLisenceFee + $vatAykor);




                // Update the total amount and currently paid money in the sonod model
                $sonod->total_amount = (float)$amountDetails['total_amount'];
                $sonod->currently_paid_money = (float)$amountDetails['currently_paid_money'];

            }else{


                $tredeLisenceFee = (float)$amountDetails['tredeLisenceFee'];

                $amountDetails['total_amount'] = (string)((float)$amountDetails['total_amount'] + $tredeLisenceFee);

                $sonod->total_amount = (float)$amountDetails['total_amount'];
                $sonod->currently_paid_money = (float)$amountDetails['currently_paid_money'];

            }








        } else {
            // For other sonod types, double the total amount and currently paid money
            $sonod->total_amount *= 2;
            $sonod->currently_paid_money *= 2;

            // Update the amount_details JSON for other sonod types
            $amountDetails['total_amount'] = (string)($amountDetails['total_amount'] * 2);
            $amountDetails['currently_paid_money'] = (string)($amountDetails['currently_paid_money'] * 2);
        }

        // Save the updated amount_details JSON
        $sonod->amount_deails = json_encode($amountDetails);

        // Update the amount in words
        $sonod->the_amount_of_money_in_words = convertAnnualIncomeToText($amountDetails['total_amount']);

        // Save the sonod model
        $sonod->save();
    }
}
