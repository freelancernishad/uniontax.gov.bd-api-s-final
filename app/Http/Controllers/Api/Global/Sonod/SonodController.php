<?php

namespace App\Http\Controllers\Api\Global\Sonod;

use Exception;
use App\Models\Sonod;
use App\Models\SonodFee;
use App\Models\Uniouninfo;
use Illuminate\Support\Str;
use App\Models\EnglishSonod;
use Illuminate\Http\Request;
use App\Models\Sonodnamelist;
use App\Models\TradeLicenseKhatFee;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SonodController extends Controller
{
    public function sonodSubmit(Request $request)
    {
        $uddoktaId = null;
        if (Auth::guard('uddokta')->check()) {
            $uddoktaId = Auth::guard('uddokta')->id();
        }

        try {

            // Log::info($request->all());

            $bnData = is_array($request->bn) ? $request->bn : json_decode($request->bn, true);
            $enData = is_array($request->en) ? $request->en : json_decode($request->en, true);

            // Ensure the data is not null before logging
            // Log::info('Decoded bnData:', !empty($bnData) ? $bnData : ['bnData' => 'null']);
            // Log::info('Decoded enData:', !empty($enData) ? $enData : ['enData' => 'null']);

            // Check if enData is present and not empty
            $hasEnData = !empty($enData);

            // Create Sonod and EnglishSonod entries (if enData is not empty)
            $sonod = $this->createSonod($bnData, $enData, $request);



            // Generate redirect URL using sonod ID
            // $urls = [
            //     "s_uri" => $bnData['s_uri'],
            //     "f_uri" => $bnData['f_uri'],
            //     "c_uri" => $bnData['c_uri'],
            // ];

            // $redirectUrl = sonodpayment($sonod->id, $urls, $hasEnData,$uddoktaId);



                $s_uri = $bnData['s_uri'];
                $f_uri = $bnData['f_uri'];
                $c_uri = $bnData['c_uri'];

            $redirectUrl= asset("/create/payment?sonod_id=$sonod->id&s_uri=$s_uri&f_uri=$f_uri&c_uri=$c_uri&hasEnData=$hasEnData&uddoktaId=$uddoktaId");



            // Return the response
            return response()->json([
                'sonod' => $sonod,
                'redirect_url' => $redirectUrl,
            ]);
        } catch (Exception $e) {
            // Handle errors and return a response with full error details
            return response()->json([
                'error' => [
                    'message' => $e->getMessage(), // The error message
                    'file' => $e->getFile(),       // The file where the error occurred
                    'line' => $e->getLine(),       // The line number where the error occurred
                    'trace' => $e->getTrace(),     // The full stack trace
                ]
            ], 400);
        }
    }


    protected function createSonod($bnData, $enData, $request)
    {


        //  $this->handleFileUploads($request, $insertData, 'ddd', '$dateFolder', '$sonodId');
        // return response()->json($insertData);

        // Process successor_list for bnData
        $successorListFormatted = $bnData['successor_list'] ?? [];
        $successor_list = json_encode($successorListFormatted);

        // Process successor_list for enData
        $enSuccessorListFormatted = $enData['successor_list'] ?? [];
        $enSuccessorList = json_encode($enSuccessorListFormatted);

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


            // Handle base64 image upload
    if (isset($bnData['image']) && $bnData['image']) {
        $insertData['image'] = $this->uploadBase64Image(
            $bnData['image'],
            $filePath,
            $dateFolder,
            $sonodId
        );
    }



        // Save the Sonod entry
        $sonod = Sonod::create($insertData);

        // Create EnglishSonod only if enData is not empty
        if (!empty($enData)) {


            $sonod = Sonod::find($sonod->id);











            // Prepare data for insertion for EnglishSonod (enData)
            $englishSonodData = array_merge($enData, [


                'applicant_gender' => $sonod->applicant_gender == 'পুরুষ' ? 'Male' : ($sonod->applicant_gender == 'মহিলা' ? 'Female' : ''),

                'applicant_national_id_number' => int_bn_to_en($sonod->applicant_national_id_number),
                'applicant_birth_certificate_number' => int_bn_to_en($sonod->applicant_birth_certificate_number),
                'applicant_holding_tax_number' => int_bn_to_en($sonod->applicant_holding_tax_number),
                'applicant_mobile' => int_bn_to_en($sonod->applicant_mobile),
                'applicant_date_of_birth' => int_bn_to_en($sonod->applicant_date_of_birth),
                'image' => $sonod->image,
                'applicant_owner_type' => $sonod->applicant_owner_type == 'ব্যক্তি মালিকানাধীন' ? 'Individual Ownership' :
                ($sonod->applicant_owner_type == 'যৌথ মালিকানা' ? 'Joint Ownership' :
                ($sonod->applicant_owner_type == 'কোম্পানী' ? 'Company' : '')),

                'applicant_vat_id_number' => $sonod->applicant_vat_id_number,
                'applicant_tax_id_number' => $sonod->applicant_tax_id_number,
                'applicant_type_of_businessKhat' => $sonod->applicant_type_of_businessKhat,
                'applicant_type_of_businessKhatAmount' => $sonod->applicant_type_of_businessKhatAmount,
                'last_years_money' => $sonod->last_years_money,
                'orthoBchor' => int_bn_to_en($sonod->orthoBchor),
                'applicant_email' => $sonod->applicant_email,

                'applicant_resident_status' => $sonod->applicant_resident_status == 'স্থায়ী' ? 'Permanent' : ($sonod->applicant_resident_status == 'অস্থায়ী' ? 'Temporary' : ''),



              'applicant_present_district' => ($district = District::where('bn_name', $sonod->applicant_present_district)->first()) ? $district->name : '',
                'applicant_present_Upazila' => ($upazila = Upazila::where('bn_name', $sonod->applicant_present_Upazila)->first()) ? $upazila->name : '',
                'applicant_present_word_number' => int_bn_to_en($sonod->applicant_present_word_number),


                // 'applicant_present_post_office' => $sonod->applicant_present_post_office,
                // 'applicant_present_village' => $sonod->applicant_present_village,


                'applicant_permanent_district' => ($district = District::where('bn_name', $sonod->applicant_permanent_district)->first()) ? $district->name : '',
                'applicant_permanent_Upazila' => ($upazila = Upazila::where('bn_name', $sonod->applicant_permanent_Upazila)->first()) ? $upazila->name : '',
                'applicant_permanent_word_number' => int_bn_to_en($sonod->applicant_permanent_word_number),


                // 'applicant_permanent_post_office' => $sonod->applicant_permanent_post_office,
                // 'applicant_permanent_village' => $sonod->applicant_permanent_village,












                'alive_status' => $sonod->alive_status,
                'format' => $sonod->format,
                'sonod_Id' => $sonod->id,
                'uniqeKey' => $uniqueKey,
                'khat' => "সনদ ফি",
                'stutus' => "Pepaid",
                'payment_status' => "Unpaid",
                'year' => date('Y'),
                'successor_list' => $enSuccessorList,
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


    if ($request->hasFile('applicant_national_id_front_attachment')) {
        $insertData['applicant_national_id_front_attachment'] = uploadDocumentsToS3(
            $request->file('applicant_national_id_front_attachment'),
            $filePath,
            $dateFolder,
            $sonodId
        );
    }

    if ($request->hasFile('applicant_national_id_back_attachment')) {
        $insertData['applicant_national_id_back_attachment'] = uploadDocumentsToS3(
            $request->file('applicant_national_id_back_attachment'),
            $filePath,
            $dateFolder,
            $sonodId
        );
    }

    if ($request->hasFile('applicant_birth_certificate_attachment')) {
        $insertData['applicant_birth_certificate_attachment'] = uploadDocumentsToS3(
            $request->file('applicant_birth_certificate_attachment'),
            $filePath,
            $dateFolder,
            $sonodId
        );
    }
}

private function uploadBase64Image($fileData, $filePath, $dateFolder, $sonodId)
{
    if ($fileData && preg_match('/^data:image\/(\w+);base64,/', $fileData, $matches)) {
        // Define the directory for the file
        $directory = "sonod/$filePath/$dateFolder/$sonodId";

        // Extract the base64 data
        $base64Data = substr($fileData, strpos($fileData, ',') + 1);

        // Decode the base64 data
        $decodedData = base64_decode($base64Data);
        if ($decodedData === false) {
            throw new \Exception("Invalid base64 data provided.");
        }

        // Determine the file extension from the MIME type
        $extension = $matches[1]; // e.g., 'png', 'jpeg'

        // Generate a unique file name
        $fileName = time() . '_' . Str::random(10) . '.' . $extension;

        // Ensure directory exists if using local storage
        if (!Storage::disk('protected')->exists($directory)) {
            Storage::disk('protected')->makeDirectory($directory);
        }

        // Store the file in the protected disk
        Storage::disk('protected')->put("$directory/$fileName", $decodedData);

        // Return the file path
        return "$directory/$fileName";
    }
    return null;
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
            $khat_id_2 = $bnData['applicant_type_of_businessKhatAmount'] ?? $enData['applicant_type_of_businessKhatAmount'] ?? 0;

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


        $currentlyPaidMoney = $totalAmount;

        // Prepare amount details for JSON encoding
        $amountDetails = json_encode([
            'total_amount' => $totalAmount + (int)$lastYearsMoney,
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
        $insertData['total_amount'] = $totalAmount + (int)$lastYearsMoney;
        $insertData['the_amount_of_money_in_words'] = convertAnnualIncomeToText($totalAmount + (int)$lastYearsMoney);
        $insertData['amount_deails'] = $amountDetails;
    }

    private function doublePriceForBoth($sonod)
    {
        // Decode the amount_details JSON
        $amountDetails = json_decode($sonod->amount_deails, true);

        // Check if the sonod_name is 'ট্রেড লাইসেন্স'
        if ($sonod->sonod_name == 'ট্রেড লাইসেন্স') {
            // Get the trade license fee and calculate 15% VAT
            $tredeLisenceFee = (float)$amountDetails['tredeLisenceFee'];
            $vatAykor = $tredeLisenceFee * 0.15; // 15% VAT

            // Add the trade license fee and VAT to the total amount
            $amountDetails['total_amount'] = (string)((float)$amountDetails['total_amount'] + $tredeLisenceFee + $vatAykor);

            // Update the total amount and currently paid money in the sonod model
            $sonod->total_amount = (float)$amountDetails['total_amount'];
            $sonod->currently_paid_money = (float)$amountDetails['currently_paid_money'];
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
            'orthoBchor', // Include orthoBchor in the response
            'renewed_id', // Include renewed_id in the response
            'renewed', // Include renewed in the response
            'hasEnData',
        ];

        // Retrieve by ID if 'id' is provided
        if ($request->has('id')) {
            $results = Sonod::select($columns)->find($request->input('id'));
        }

        // Search by `sonod_Id` and `sonod_name` if both are provided
        $sonodId = $request->input('sonod_Id');
        $sonodName = $request->input('sonod_name');

        if ($sonodId && $sonodName) {
            $results = Sonod::select($columns)
                ->where('sonod_Id', $sonodId)
                ->where('sonod_name', $sonodName)
                ->first();
        }


            if ($results) {
                // Calculate the current orthoBchor
                $currentYear = date('Y');
                $currentMonth = date('m');

                if ($currentMonth >= 7) {
                    $currentOrthoBchor = $currentYear . '-' . substr(($currentYear + 1), -2);
                } else {
                    $currentOrthoBchor = ($currentYear - 1) . '-' . substr($currentYear, -2);
                }

                // Check if orthoBchor is not current
                $isNotCurrentOrthoBchor = ($results->orthoBchor !== $currentOrthoBchor);


                // Check if renewed_id is not null but renewed is null
                $isRenewable = (
                    ($results->renewed_id && !$results->renewed) || // Case 1
                    (!$results->renewed_id && !$results->renewed)    // Case 2
                );
                // return response()->json($isRenewable);
                // Set renew_able flag
                $results->renew_able = ($isNotCurrentOrthoBchor && $isRenewable);

                // Initialize download URLs
                $results->download_url = '';
                $results->download_url_en = '';

                // Check if the Sonod is approved and paid
                if ($results->stutus === 'approved' && $results->payment_status === 'Paid') {
                    // Generate the download URL
                    if ($results->renew_able) {
                        // If renew_able is true, generate the download URL for the renewed Sonod (if applicable)
                        if ($results->renewed_id && $results->renewed) {
                            $results->download_url = url("sonod/download/$results->renewed_id");
                            if ($results->hasEnData) {
                                $results->download_url_en = url("sonod/download/$results->renewed_id?en=true");
                            }
                        }
                    } else {
                        // If renew_able is false, generate the download URL for the main Sonod
                        $results->download_url = url("sonod/download/$results->id");
                        if ($results->hasEnData) {
                            $results->download_url_en = url("sonod/download/$results->id?en=true");
                        }
                    }
                }

                return response()->json($results);
            }






        return response()->json([
            'error' => 'Invalid search parameters provided',
        ], 400);
    }


    public function renewSonod(Request $request, $id)
    {
        try {
            // Extract URIs from the request
            $urls = [
                "s_uri" => $request->input('s_uri'),
                "f_uri" => $request->input('f_uri'),
                "c_uri" => $request->input('c_uri'),
            ];

            // Validate that URIs are provided in the request
            if (empty($urls['s_uri']) || empty($urls['f_uri']) || empty($urls['c_uri'])) {
                return response()->json(['error' => 'Missing URIs in the request'], 400);
            }

            // Retrieve existing Sonod data
            $existingSonod = Sonod::find($id);

            if (!$existingSonod) {
                return response()->json(['error' => 'Sonod not found'], 404);
            }



            // Check if the Sonod is approved and paid
            if ($existingSonod->stutus !== 'approved' || $existingSonod->payment_status !== 'Paid') {
                return response()->json(['error' => 'Sonod must be approved and paid to be renewed'], 400);
            }


            // Check if renewed_id exists
            if ($existingSonod->renewed_id) {
                // If renewed_id exists, check the renewed status
                if ($existingSonod->renewed) {
                    // If both renewed_id and renewed are true, return a message
                    return response()->json(['message' => 'Sonod has already been renewed.'], 200);
                } else {
                    // If renewed_id exists but renewed is false, generate payment URL using renewed_id
                    $renewedSonod = Sonod::find($existingSonod->renewed_id);

                    if ($renewedSonod) {
                        $uddoktaId = null;
                        if (Auth::guard('uddokta')->check()) {
                            $uddoktaId = Auth::guard('uddokta')->id();
                        }

                        // Generate payment URL directly using renewed_id
                        $redirectUrl = sonodpayment($renewedSonod->id, $urls, $renewedSonod->hasEnData, $uddoktaId);

                        return response()->json([
                            'message' => 'Sonod renewal pending. Redirecting to payment for the renewed Sonod.',
                            'redirect_url' => $redirectUrl,
                        ], 200);
                    } else {
                        return response()->json(['error' => 'Renewed Sonod not found'], 404);
                    }
                }
            }

            // If both renewed_id and renewed are false, proceed with the normal renewal process

            // Calculate the current orthoBchor
            $currentYear = date('Y');
            $currentMonth = date('m');

            if ($currentMonth >= 7) {
                $currentOrthoBchor = $currentYear . '-' . substr(($currentYear + 1), -2);
            } else {
                $currentOrthoBchor = ($currentYear - 1) . '-' . substr($currentYear, -2);
            }

            // Validate that the existing Sonod is from the previous orthoBchor
            if ($existingSonod->orthoBchor === $currentOrthoBchor) {
                return response()->json(['error' => 'Sonod from the current orthoBchor cannot be renewed'], 400);
            }

            // Generate new sonod_Id for the renewed Sonod
            $sonodId = (string) sonodId($existingSonod->unioun_name, $existingSonod->sonod_name, $currentOrthoBchor);

            // Replicate existing Sonod data
            $newSonod = $existingSonod->replicate();

            // Generate a new unique key for the renewed Sonod
            do {
                $uniqueKey = md5(uniqid($existingSonod->unioun_name . $existingSonod->sonod_name . microtime(), true));
                $existingSonodWithKey = Sonod::where('uniqeKey', $uniqueKey)->first();
            } while ($existingSonodWithKey);

            // Modify specific fields for the new Sonod
            $newSonod->orthoBchor = $currentOrthoBchor; // Set to the current orthoBchor
            $newSonod->sonod_Id = $sonodId;
            $newSonod->uniqeKey = $uniqueKey; // Assign the new unique key
            $newSonod->stutus = 'Pepaid'; // Assuming 'Pepaid' is the correct status
            $newSonod->payment_status = 'Unpaid';
            $newSonod->renewed_id = null; // Ensure the new Sonod is not marked as renewed
            $newSonod->renewed = 0; // Mark as not renewed

            // Save the new Sonod record
            $newSonod->save();

            // Update the existing Sonod to mark it as renewed
            $existingSonod->update([
                'renewed_id' => $newSonod->id,
                // 'renewed' => 1,
            ]);

            // Check if the existing Sonod has an associated EnglishSonod
            $existingEnglishSonod = EnglishSonod::where('sonod_Id', $existingSonod->id)->first();

            if ($existingEnglishSonod) {
                // Replicate the existing EnglishSonod data
                $newEnglishSonod = $existingEnglishSonod->replicate();

                // Generate a new unique key for the renewed EnglishSonod
                do {
                    $englishUniqueKey = md5(uniqid($existingSonod->unioun_name . $existingSonod->sonod_name . microtime(), true));
                    $existingEnglishSonodWithKey = EnglishSonod::where('uniqeKey', $englishUniqueKey)->first();
                } while ($existingEnglishSonodWithKey);

                // Modify specific fields for the new EnglishSonod
                $newEnglishSonod->sonod_Id = $newSonod->id; // Link to the new Sonod
                $newEnglishSonod->uniqeKey = $englishUniqueKey; // Assign the new unique key
                $newEnglishSonod->stutus = 'Pepaid'; // Update status
                $newEnglishSonod->payment_status = 'Unpaid'; // Update payment status

                // Save the new EnglishSonod record
                $newEnglishSonod->save();
            }

            $uddoktaId = null;
            if (Auth::guard('uddokta')->check()) {
                $uddoktaId = Auth::guard('uddokta')->id();
            }

            // Generate payment URL directly (similar to sonodSubmit)
            $redirectUrl = sonodpayment($newSonod->id, $urls, $newSonod->hasEnData, $uddoktaId);

            // Return the response with the new Sonod data and payment URL
            return response()->json([
                'message' => 'Sonod renewed successfully',
                // 'new_sonod' => $newSonod,
                // 'new_english_sonod' => $newEnglishSonod ?? null, // Include the new EnglishSonod if it exists
                'redirect_url' => $redirectUrl,
            ], 200);

        } catch (Exception $e) {
            // Handle errors and return a response
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }




    function creatingEkpayUrl(Request $request)
    {
        // return response()->json('tst');
        // Extract parameters from the request URL
        $sonodId = $request->query('sonod_id'); // Get sonod_id from the URL query string
        $sUri = $request->query('s_uri');       // Get s_uri from the URL query string
        $fUri = $request->query('f_uri');       // Get f_uri from the URL query string
        $cUri = $request->query('c_uri');       // Get c_uri from the URL query string
        $hasEnData = $request->query('hasEnData'); // Get hasEnData from the URL query string
        $uddoktaId = $request->query('uddoktaId'); // Get uddoktaId from the URL query string

        // Prepare the URLs array
        $urls = [
            "s_uri" => $sUri,
            "f_uri" => $fUri,
            "c_uri" => $cUri,
        ];

        // Generate the redirect URL using the sonodpayment function
        $redirectUrl = sonodpayment($sonodId, $urls, $hasEnData, $uddoktaId);

        // Return the redirect URL (or use it as needed)
        return response()->json($redirectUrl);
    }




}
