<?php

namespace App\Http\Controllers\Api\Global\Sonod;

use Exception;
use App\Models\Sonod;
use App\Models\SonodFee;
use App\Models\Uniouninfo;
use App\Models\EnglishSonod;
use Illuminate\Http\Request;
use App\Models\Sonodnamelist;
use App\Models\TradeLicenseKhatFee;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class SonodController extends Controller
{
    public function sonodSubmit(Request $request)
    {
        try {
            // Extract bn and en data from the request
            $bnData = $request->bn; // Data for Sonod (Bengali)
            $enData = $request->en; // Data for EnglishSonod (English)

            // Check if enData is present and not empty
            $hasEnData = !empty($enData);

            // Create Sonod and EnglishSonod entries (if enData is not empty)
            $sonod = $this->createSonod($bnData, $enData, $request);

            // Generate redirect URL using sonod ID
            $urls = [
                "s_uri" => $bnData['s_uri'],
                "f_uri" => $bnData['f_uri'],
                "c_uri" => $bnData['c_uri'],
            ];

            $redirectUrl = sonodpayment($sonod->id, $urls, $hasEnData);

            // Return the response
            return response()->json([
                'sonod' => $sonod,
                'redirect_url' => $redirectUrl,
            ]);
        } catch (Exception $e) {
            // Handle errors and return a response
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    protected function createSonod($bnData, $enData, $request)
    {
        // Process successor_list for bnData
        $successorList = [];
        foreach ($bnData as $key => $value) {
            if (strpos($key, 'successor_list') === 0) {
                // Extract index and field name
                preg_match('/successor_list\[(\d+)\]\.(w_name|w_relation|w_age|w_nid|w_note)/', $key, $matches);
                if ($matches) {
                    $index = $matches[1];
                    $field = $matches[2];
                    $successorList[$index][$field] = $value;
                }
            }
        }

        // Convert to the desired JSON format
        $successorListFormatted = array_values($successorList);
        $successor_list = json_encode($successorListFormatted);

        // Fetch the English name of the Sonod
        $sonodName = $bnData['sonod_name'];
        $sonodEnName = Sonodnamelist::where('bnname', $sonodName)->first();
        if (!$sonodEnName) {
            throw new Exception('No data found for the given Sonod name.');
        }

        $filePath = str_replace(' ', '_', $sonodEnName->enname);
        $dateFolder = date("Y/m/d");

        // Generate unique key if not provided
        $unionName = $bnData['unioun_name'];
        do {
            $uniqueKey = md5(uniqid($unionName . $sonodName . microtime(), true));
            $existingSonod = Sonod::where('uniqeKey', $uniqueKey)->first();
        } while ($existingSonod);

        $sonodId = $request->has('sonod_id')
            ? $request->sonod_id
            : (string) sonodId($unionName, $sonodName, getOrthoBchorYear());

        // Prepare data for insertion for Sonod (bnData)
        $insertData = array_merge($bnData, [
            'applicant_type_of_businessKhat' => $bnData['applicant_type_of_businessKhat'] ?? null,
            'applicant_type_of_businessKhatAmount' => $bnData['applicant_type_of_businessKhatAmount'] ?? 0,
            'uniqeKey' => $uniqueKey,
            'khat' => "সনদ ফি",
            'stutus' => "Pepaid",
            'payment_status' => "Unpaid",
            'year' => date('Y'),
            'hasEnData' => !empty($enData), // Set hasEnData based on whether enData is present
        ]);

        $insertData = array_merge($insertData, $this->prepareSonodData($request, $sonodName, $successor_list, $unionName, $sonodId));

        // Handle file uploads securely
        $this->handleFileUploads($request, $insertData, $filePath, $dateFolder, $sonodId);

        // Check if annual income is provided and process accordingly
        if (isset($bnData['Annual_income'])) {
            $insertData['Annual_income'] = $bnData['Annual_income'];
            $insertData['Annual_income_text'] = convertAnnualIncomeToText($bnData['Annual_income']);
        }

        // Handle the status and charges
        $this->handleCharges($bnData, $enData, $sonodEnName, $insertData);

        // Save the Sonod entry
        $sonod = Sonod::create($insertData);

        // Create EnglishSonod only if enData is not empty
        if (!empty($enData)) {
            // Prepare data for insertion for EnglishSonod (enData)
            $englishSonodData = array_merge($enData, [
                'sonod_Id' => $sonod->id, // Link to the Sonod entry
                'uniqeKey' => $uniqueKey, // Same unique key as Sonod
                'khat' => "সনদ ফি",
                'stutus' => "Pepaid",
                'payment_status' => "Unpaid",
                'year' => date('Y'),
            ]);

            // Check if EnglishSonod already exists for this Sonod
            $existingEnglishSonod = EnglishSonod::where('sonod_Id', $sonod->id)->first();

            if ($existingEnglishSonod) {
                $existingEnglishSonod->update($englishSonodData);
            } else {
                EnglishSonod::create($englishSonodData);
            }

            // Double the price if both Sonod and EnglishSonod are created
            $this->doublePriceForBoth($sonod);
        }

        return $sonod; // Return only the sonod entry
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

    private function handleFileUploads($request, &$insertData, $filePath, $dateFolder, $sonodId)
    {
        // Handle file uploads with optimized code
        $this->uploadFile($request->image, $insertData, 'image', $filePath, $dateFolder, $sonodId);
        $this->uploadFile($request->applicant_national_id_front_attachment, $insertData, 'applicant_national_id_front_attachment', $filePath, $dateFolder, $sonodId);
        $this->uploadFile($request->applicant_national_id_back_attachment, $insertData, 'applicant_national_id_back_attachment', $filePath, $dateFolder, $sonodId);
        $this->uploadFile($request->applicant_birth_certificate_attachment, $insertData, 'applicant_birth_certificate_attachment', $filePath, $dateFolder, $sonodId);
    }

    private function uploadFile($fileData, &$insertData, $field, $filePath, $dateFolder, $sonodId)
    {
        if (count(explode(';', $fileData)) > 1) {
            // $insertData[$field] = uploadFileToS3($fileData, "sonod/$filePath/$dateFolder/$sonodId/");
        }
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

        // Check if it's a 'ট্রেড লাইসেন্স' and retrieve the PesaKor fee
        if ($sonodName == 'ট্রেড লাইসেন্স') {
            $khat_id_1 = $bnData['applicant_type_of_businessKhat'] ?? $enData['applicant_type_of_businessKhat'] ?? null;
            $khat_id_2 = $bnData['applicant_type_of_businessKhatAmount'] ?? $enData['applicant_type_of_businessKhatAmount'] ?? null;

            $pesaKorFee = TradeLicenseKhatFee::where([
                'khat_id_1' => $khat_id_1,
                'khat_id_2' => $khat_id_2
            ])->first();

            $pesaKor = $pesaKorFee ? $pesaKorFee->fee : 0;
            $tradeVatAmount = ($sonodFee * $tradeVat) / 100;
        } else {
            $pesaKor = 0;
            $tradeVatAmount = 0;
        }

        // Calculate total amount and currently paid money
        $totalAmount = $sonodFee + $tradeVatAmount + $pesaKor;
        $currentlyPaidMoney = $totalAmount - $lastYearsMoney;

        // Prepare amount details for JSON encoding
        $amountDetails = json_encode([
            'total_amount' => $totalAmount,
            'pesaKor' => (string)$pesaKor,
            'tredeLisenceFee' => (string)$sonodFee,
            'vatAykor' => (string)$tradeVat,
            'khat' => null,
            'last_years_money' => (string)$lastYearsMoney,
            'currently_paid_money' => (string)$currentlyPaidMoney
        ]);

        // Update insertData with calculated values
        $insertData['last_years_money'] = $lastYearsMoney;
        $insertData['currently_paid_money'] = $currentlyPaidMoney;
        $insertData['total_amount'] = $totalAmount;
        $insertData['the_amount_of_money_in_words'] = convertAnnualIncomeToText($totalAmount);
        $insertData['amount_deails'] = $amountDetails;
    }

    private function doublePriceForBoth($sonod)
    {
        // Double the price if both Sonod and EnglishSonod are created
        $sonod->total_amount *= 2;
        $sonod->currently_paid_money *= 2;
        $sonod->save();

        // Update amount_details JSON
        $amountDetails = json_decode($sonod->amount_deails, true);
        $amountDetails['total_amount'] = (string)($amountDetails['total_amount'] * 2);
        $amountDetails['currently_paid_money'] = (string)($amountDetails['currently_paid_money'] * 2);
        $sonod->amount_deails = json_encode($amountDetails);
        $sonod->the_amount_of_money_in_words = convertAnnualIncomeToText($amountDetails['total_amount']);
        $sonod->save();
    }

    private function sendNotification($sonod)
    {
        // Send notification to the union's secretary
        $notificationData = [
            'union' => $sonod->unioun_name,
            'roles' => 'Secretary'
        ];

        $notificationCount = Notifications::where($notificationData)->count();
        if ($notificationCount > 0) {
            $actionUrl = makeshorturl(url('/secretary/approve/' . $sonod->id));
            $notification = Notifications::where($notificationData)->latest()->first();
            $data = json_encode([
                'to' => $notification->key,
                'notification' => [
                    'body' => $sonod->applicant_name . ' একটি ' . $sonod->sonod_name . ' এর নুতুন আবেদন করেছে',
                    'title' => 'সনদ নং ' . int_en_to_bn($sonod->sonod_Id),
                    'icon' => asset('assets/img/bangladesh-govt.png'),
                    'click_action' => $actionUrl
                ]
            ]);
            pushNotification($data);
        }
    }

    public function findSonod(Request $request)
    {
        // Columns to select
        $columns = [
            'id',
            'unioun_name',
            'year',
            'sonod_Id',
            'sonod_name',
            'applicant_national_id_number',
            'applicant_birth_certificate_number',
            'applicant_name',
            'applicant_date_of_birth',
            'applicant_gender',
            'payment_status',
            'stutus',
            'successor_list',
        ];

        // Retrieve by ID if 'id' is provided
        if ($request->has('id')) {
            $sonod = Sonod::select($columns)->find($request->input('id'));

            if ($sonod) {
                return response()->json([
                    'success' => true,
                    'data' => $sonod,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Sonod not found by ID',
            ], 404);
        }

        // Search by `sonod_Id` and `sonod_name` if both are provided
        $sonodId = $request->input('sonod_Id');
        $sonodName = $request->input('sonod_name');

        if ($sonodId && $sonodName) {
            $results = Sonod::select($columns)
                ->where('sonod_Id', $sonodId)
                ->where('sonod_name', $sonodName)
                ->first();

            if ($results) {
                return response()->json($results);
            }

            return response()->json([
                'error' => 'Sonod not found by sonod_Id and sonod_name',
            ], 404);
        }

        return response()->json([
            'error' => 'Invalid search parameters provided',
        ], 400);
    }
}
