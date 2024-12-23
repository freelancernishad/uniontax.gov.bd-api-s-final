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
use App\Http\Controllers\Controller;


class SonodController extends Controller
{
    public function sonodSubmit(Request $request)
    {




        // Extract necessary request data
        $sonodName = $request->sonod_name;
        $unionName = $request->unioun_name;
        $successors = json_encode($request->successor_list);
        $sonodEnName = Sonodnamelist::where('bnname', $sonodName)->first();
        if (!$sonodEnName) {
            return response()->json([
                'error' => 'No data found for the given Sonod name.'
            ], 404); // Return a 404 Not Found status
        }
        $filePath = str_replace(' ', '_', $sonodEnName->enname);
        $dateFolder = date("Y/m/d");

        // Generate unique key if not provided
        do {
            $uniqueKey = md5(uniqid($unionName . $sonodName . microtime(), true));
            $existingSonod = Sonod::where('uniqeKey', $uniqueKey)->first();
        } while ($existingSonod);

        $sonodId = $request->has('sonod_id')
            ? $request->sonod_id
            : (string) sonodId($unionName, $sonodName, getOrthoBchorYear());

        // Prepare data for insertion
        $insertData = $request->except([
            'sonod_Id', 'image', 'applicant_national_id_front_attachment',
            'applicant_national_id_back_attachment', 'applicant_birth_certificate_attachment',
            'successors', 'charages', 'Annual_income', 'applicant_type_of_businessKhat',
            'applicant_type_of_businessKhatAmount', 'orthoBchor'
        ]);

        $insertData['applicant_type_of_businessKhat'] = $request->applicant_type_of_businessKhat;
        $insertData['applicant_type_of_businessKhatAmount'] = $request->applicant_type_of_businessKhatAmount ?? 0;
        $insertData['uniqeKey'] = $uniqueKey;
        $insertData['khat'] = "সনদ ফি";
        $insertData['stutus'] = "Pepaid";
        $insertData['payment_status'] = "Unpaid";
        $insertData['year'] = date('Y');

        $insertData = array_merge($insertData, $this->prepareSonodData($request, $sonodName, $successors, $unionName, $sonodId));

        // Handle file uploads securely
        $this->handleFileUploads($request, $insertData, $filePath, $dateFolder, $sonodId);

        // Check if annual income is provided and process accordingly
        if ($request->Annual_income) {
            $insertData['Annual_income'] = $request->Annual_income;
            $insertData['Annual_income_text'] = convertAnnualIncomeToText($request->Annual_income);
        }

        // Handle the status and charges
        $this->handleCharges($request, $sonodEnName, $insertData);

        try {

            $urls = [
                "s_uri" => $request->s_uri,
                "f_uri" => $request->f_uri,
                "c_uri" => $request->c_uri,
            ];

            // Process EnglishSonod if requested
            $sonod = null;
            $englishSonod = null;
            if ($request->english) {
                $englishSonodData = $insertData;
                $englishSonodData['sonod_Id'] = $request->sonod_Id;
                // return response()->json($englishSonodData);

                // Check if EnglishSonod already exists for this Sonod
                $existingEnglishSonod = EnglishSonod::where('sonod_Id', $request->sonod_Id)->first();

                if ($existingEnglishSonod) {
                    $existingEnglishSonod->update($englishSonodData);
                    $englishSonod = $existingEnglishSonod;
                } else {
                    $englishSonod = EnglishSonod::create($englishSonodData);
                }
                $redirectUrl = sonodpayment($englishSonod->id, $urls);
            }else{
                // Save the Sonod entry
                $sonod = Sonod::create($insertData);
                $redirectUrl = sonodpayment($sonod->id, $urls);
            }




            // Return the response
            return response()->json([
                'sonod' => $sonod,
                'english_sonod' => $englishSonod,
                'redirect_url' => $redirectUrl,
            ]);
        } catch (Exception $e) {
            // Handle errors and return a response
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    private function prepareSonodData($request, $sonodName, $successors, $unionName, $sonodId)
    {
        $insertData = [];

        // Specific adjustments based on sonod name
        if ($sonodName == 'একই নামের প্রত্যয়ন' || $sonodName == 'বিবিধ প্রত্যয়নপত্র') {
            $insertData['sameNameNew'] = 1;
        }

        // Set the orthoBchor based on current year/month
        $insertData['orthoBchor'] = getOrthoBchorYear();
        // $insertData['orthoBchor'] = ($sonodName == 'ট্রেড লাইসেন্স') ? $request->orthoBchor : getOrthoBchorYear();

        // Set additional fields from the union info
        $unionInfo = Uniouninfo::where('short_name_e', $unionName)->latest()->first();
        $insertData['chaireman_name'] = $unionInfo->c_name;
        $insertData['c_email'] = $unionInfo->c_email;
        $insertData['chaireman_sign'] = $unionInfo->c_signture;
        $insertData['chaireman_type'] = $unionInfo->c_type;

        // Add successor list
        $insertData['successor_list'] = $successors;

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



    private function handleCharges($request, $sonodnamelist, &$insertData)
    {
        $tradeVat = 15;
        $lastYearsMoney = $request->last_years_money;
        $sonodName = $request->sonod_name;
        $uniounName = $request->unioun_name;

        // Fetch the corresponding sonod fee from the SonodFee table
        $sonodFeeRecord = SonodFee::where([
            'service_id' => $sonodnamelist->service_id,
            'unioun' => $uniounName
        ])->first();

        if (!$sonodFeeRecord) {
            return response()->json(['message' => 'Sonod fee not found.'], 404);
        }

        $sonodFee = $sonodFeeRecord->fees; // Get the fee from the SonodFee table

        // Check if it's a 'ট্রেড লাইসেন্স' and retrieve the PesaKor fee
        if ($sonodName == 'ট্রেড লাইসেন্স') {
            // Assuming the 'Sonod' model has fields 'applicant_type_of_businessKhat' and 'applicant_type_of_businessKhatAmount'
            $khat_id_1 = $request->applicant_type_of_businessKhat; // Applicant type of business Khat
            $khat_id_2 = $request->applicant_type_of_businessKhatAmount; // Applicant type of business Khat Amount

            // Retrieve the corresponding fee from the TradeLicenseKhatFee model
            $pesaKorFee = TradeLicenseKhatFee::where([
                'khat_id_1' => $khat_id_1,
                'khat_id_2' => $khat_id_2
            ])->first();

            // If a matching fee is found, use it as the PesaKor fee
            $pesaKor = $pesaKorFee ? $pesaKorFee->fee : 0; // Default to 0 if no fee is found
        } else {
            $pesaKor = 0; // If it's not 'ট্রেড লাইসেন্স', no PesaKor fee
        }

        // Calculating the VAT amount (assumed to be a percentage)
        $tradeVatAmount = ($sonodFee * $tradeVat) / 100;

        // Add PesaKor fee if it exists, otherwise just sum the Sonod fee and VAT
        $totalAmount = $sonodFee + $tradeVatAmount + $pesaKor;

        // Calculating the money currently paid
        $currentlyPaidMoney = $totalAmount - $lastYearsMoney;

        // Encoding the amount details as JSON with the required structure
        $amountDetails = json_encode([
            'total_amount' => $totalAmount,
            'pesaKor' => (string)$pesaKor, // Ensure it's a string for the desired format
            'tredeLisenceFee' => (string)$sonodFee, // 'tredeLisenceFee' maps to the 'sonod_fee'
            'vatAykor' => (string)$tradeVat, // VAT calculation as a string
            'khat' => null, // As specified, this is null
            'last_years_money' => (string)$lastYearsMoney, // Ensuring the fields are returned as strings
            'currently_paid_money' => (string)$currentlyPaidMoney // Ensure it's a string
        ]);

        // Inserting the calculated data into the insertData array
        $insertData['last_years_money'] = $lastYearsMoney;
        $insertData['currently_paid_money'] = $currentlyPaidMoney;
        $insertData['total_amount'] = $totalAmount;
        $insertData['the_amount_of_money_in_words'] = convertAnnualIncomeToText($totalAmount);
        $insertData['amount_deails'] = $amountDetails;
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


     /**
     * Search Sonod by `sonod_Id` and `sonod_name` or retrieve by `id`.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
