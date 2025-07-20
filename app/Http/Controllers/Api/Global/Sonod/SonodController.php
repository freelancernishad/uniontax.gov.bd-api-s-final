<?php

namespace App\Http\Controllers\Api\Global\Sonod;

use Exception;
use App\Models\Sonod;
use App\Models\SonodFee;
use App\Models\SonodFile;
use App\Models\Uniouninfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\EnglishSonod;
use Illuminate\Http\Request;
use App\Models\Sonodnamelist;
use App\Models\SonodHoldingOwner;
use App\Models\TradeLicenseKhatFee;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Services\SonodCreatorService;
use Illuminate\Support\Facades\Storage;
use App\Services\EnglishSonodCreatorService;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use Devfaysal\BangladeshGeocode\Models\District;

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
            $holdingData = is_array($request->holding_details) ? $request->holding_details : json_decode($request->holding_details, true);

            // Ensure the data is not null before logging
            // Log::info('Decoded bnData:', !empty($bnData) ? $bnData : ['bnData' => 'null']);
            // Log::info('Decoded enData:', !empty($enData) ? $enData : ['enData' => 'null']);

            // Check if enData is present and not empty
            $hasEnData = !empty($enData);

            // Create Sonod and EnglishSonod entries (if enData is not empty)
            $sonod = $this->createSonod($bnData, $enData,$holdingData, $request);



            // Generate redirect URL using sonod ID
            // $urls = [
            //     "s_uri" => $bnData['s_uri'],
            //     "f_uri" => $bnData['f_uri'],
            //     "c_uri" => $bnData['c_uri'],
            // ];

            // $redirectUrl = sonodpayment($sonod->id, $urls, $hasEnData,$uddoktaId);



                $s_uri = $bnData['s_uri'] ?? $enData['s_uri'];
                $f_uri = $bnData['f_uri'] ?? $enData['f_uri'];
                $c_uri = $bnData['c_uri'] ?? $enData['c_uri'];

                $onlyEnglish = false;
                if($request->has('sonod_id') && $request->sonod_id){
                    $onlyEnglish = true;
                }

            $redirectUrl= asset("/create/payment?sonod_id=$sonod->id&s_uri=$s_uri&f_uri=$f_uri&c_uri=$c_uri&hasEnData=$hasEnData&uddoktaId=$uddoktaId&only_english=$onlyEnglish");



            // Return the response
            return response()->json([
                'sonod' => $sonod->load('holdingOwners'),
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


protected function createSonod($bnData, $enData, $holdingData, $request)
{
    $sonodCreator = new SonodCreatorService();
    $englishSonodCreator = new EnglishSonodCreatorService();

    $sonod = null;

    // যদি শুধু ইংরেজি ডাটা থাকে, বাংলা ডাটা না থাকে
    if (empty($bnData) && !empty($enData)) {
        // প্রথমে sonod_id দিয়ে খুঁজে দেখুন
        $sonod = null;
        if ($request->has('sonod_id') && $request->sonod_id) {
            $sonod = Sonod::find($request->sonod_id);
        }
        // না পেলে নতুন করে তৈরি করুন
        if (!$sonod) {
            $sonod = $sonodCreator->create($enData, [], $holdingData, $request);
        }
        // তারপর ইংরেজি সনদ তৈরি বা আপডেট করুন
        $englishSonodCreator->createOrUpdate($sonod, $enData,'only_en', $request);

        return $sonod;
    }

    // বাংলা সনদ তৈরি / আপডেট
    if (!empty($bnData)) {
        $sonod = $sonodCreator->create($bnData, $enData, $holdingData, $request);
    }

    // ইংরেজি সনদ তৈরি / আপডেট
    if (!empty($enData)) {
        if (!$sonod) {
            if ($request->has('sonod_id') && $request->sonod_id) {
                $sonod = Sonod::find($request->sonod_id);
                if (!$sonod) {
                    throw new \Exception("Sonod not found for given sonod_id.");
                }
            } else {
                throw new \Exception("Sonod ID missing to create EnglishSonod.");
            }
        }
        $englishSonodCreator->createOrUpdate($sonod, $enData,'both', $request);
    }

    return $sonod;
}












    // private function sendNotification($sonod)
    // {
    //     // Send notification to the union's secretary
    //     $notificationData = [
    //         'union' => $sonod->unioun_name,
    //         'roles' => 'Secretary'
    //     ];

    //     $notificationCount = Notifications::where($notificationData)->count();
    //     if ($notificationCount > 0) {
    //         $actionUrl = makeshorturl(url('/secretary/approve/' . $sonod->id));
    //         $notification = Notifications::where($notificationData)->latest()->first();
    //         $data = json_encode([
    //             'to' => $notification->key,
    //             'notification' => [
    //                 'body' => $sonod->applicant_name . ' একটি ' . $sonod->sonod_name . ' এর নুতুন আবেদন করেছে',
    //                 'title' => 'সনদ নং ' . int_en_to_bn($sonod->sonod_Id),
    //                 'icon' => asset('assets/img/bangladesh-govt.png'),
    //                 'click_action' => $actionUrl
    //             ]
    //         ]);
    //         pushNotification($data);
    //     }
    // }

    function callSonodApi($sonodId, $sonodName = 'নাগরিকত্ব সনদ')
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://oldbirol.uniontax.gov.bd/api/sonod/search',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "sonod_Id": "'.$sonodId.'",
            "sonod_name": "'.$sonodName.'"
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);


        curl_close($curl);
        $response =   json_decode($response);


        $additionalData = [
            'id' => $response->id,
            'unioun_name' => $response->unioun_name,
            'year' => $response->year,
            'sonod_Id' => $response->sonod_Id,
            'sonod_name' => $response->sonod_name,
            'applicant_national_id_number' => $response->applicant_national_id_number,
            'applicant_birth_certificate_number' => $response->applicant_birth_certificate_number,
            'applicant_name' => $response->applicant_name,
            'applicant_date_of_birth' => $response->applicant_date_of_birth,
            'applicant_gender' => $response->applicant_gender,
            'payment_status' => $response->payment_status,
            'stutus' => $response->stutus,
            'successor_list' => $response->successor_list,
            'orthoBchor' => $response->orthoBchor,
            'renewed_id' => $response->renewed_id,
            'renewed' => $response->renewed,
            'hasEnData' => 0,
            'renew_able' => 0,
            'download_url' => "https://oldbirol.uniontax.gov.bd/sonod/d/$response->id",
            'download_url_en' => "",
        ];
        return $additionalData;





    }

    public function findMySonod(Request $request)
    {





        $searchTerm = $request->input('query'); // Get search term

        if (!$searchTerm) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide a search term.'
            ], 400);
        }

        // Search for Sonods if any field matches
        $sonods = Sonod::select(
                'id',
                'sonod_name',
                'uniqeKey',
                'unioun_name',
                'applicant_name',
                'applicant_father_name',
                'applicant_present_word_number',
                'created_at',
                'stutus',
                'payment_status',
                'sonod_Id',
                'prottoyon',
                'hasEnData',
                'created_at',
                'updated_at'
            )
            ->where('sonod_Id', 'LIKE', "%{$searchTerm}%")
            ->orWhere('applicant_name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('applicant_national_id_number', 'LIKE', "%{$searchTerm}%")
            ->orWhere('applicant_birth_certificate_number', 'LIKE', "%{$searchTerm}%")
            ->orWhere('applicant_passport_number', 'LIKE', "%{$searchTerm}%")
            ->orWhere('applicant_mobile', 'LIKE', "%{$searchTerm}%")
            ->orderBy('created_at', 'desc') // Order by latest created_at
            ->paginate(10); // Get list directly

        if ($sonods->isNotEmpty()) {
            return response()->json([
                'status' => 'success',
                'data' => $sonods
            ]);
        }

        return response()->json([
            'status' => 'not_found',
            'message' => 'No Sonod found with the provided information.'
        ], 404);
    }



    function findMySonodForReApplication(Request $request)
    {
        $searchTerm = $request->input('id'); // Get search term

        if (!$searchTerm) {
            return response()->json([
            'status' => 'error',
            'message' => 'Please provide a search term.'
            ], 400);
        }

        // Search for the Sonod by unique key and get the first match
        $sonod = Sonod::where('uniqeKey', $searchTerm)->first(); // Get the first match

        if ($sonod) {
            return response()->json($sonod);
        }

        return response()->json([
            'status' => 'not_found',
            'message' => 'No Sonod found with the provided unique key.'
        ], 404);
    }



    public function findSonod(Request $request)
    {


        $sonodId = $request->input('sonod_Id');
        $sonodName = $request->input('sonod_name');
        // List of allowed first 6 digits
        $allowedFirstDigits = [
            '271709', '271766', '271747', '271795',
            '271728', '271719', '271738', '271757',
            '271776', '271785', '271796', '271768'
        ];
        // Extract first 6 digits
        $firstSixDigits = substr($sonodId, 0, 6);
        // Check if first 6 digits match allowed list
        if (in_array($firstSixDigits, $allowedFirstDigits)) {
            $response = $this->callSonodApi($sonodId, $sonodName);
            return response()->json($response);
        }








        // Columns to select
        $columns = [
            'id',
            'unioun_name',
            'year',
            'sonod_Id',
            'sonod_name',
            'applicant_national_id_number',
            'applicant_birth_certificate_number',
                    'applicant_type_of_businessKhat',
        'applicant_type_of_businessKhatAmount',
        'amount_deails',
        'bokeya',
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
        if ($request->has('uniqeKey')) {
            $results = Sonod::select($columns)->where("uniqeKey" , $request->input('uniqeKey'))->first();
        }

        // Retrieve by ID if 'id' is provided
        if ($request->has('id')) {
            $results = Sonod::select($columns)->find($request->input('id'));
        }

        // Search by `sonod_Id` and `sonod_name` if both are provided



        if ($sonodId && $sonodName) {

            $results = Sonod::select($columns)
                ->where('sonod_Id', $sonodId)
                ->where('sonod_name', $sonodName)
                ->first();

        }






        // Check if results are empty and return a message if no data is found
        if (!$results) {
            return response()->json([
                'message' => 'No data found for the provided criteria.',
                'data' => null
            ], 404);  // 404 Not Found HTTP status code
        }

        $amount_deails = json_decode($results->amount_deails, true);
        $pesaKor = $amount_deails['pesaKor'] ?? 0;
        $khat_fees = TradeLicenseKhatFee::where('khat_id_1', $results->applicant_type_of_businessKhat)
            ->where('khat_id_2', $results->applicant_type_of_businessKhatAmount)
            ->first()->fee ?? 0;

        Log::info($pesaKor);
        Log::info($khat_fees);

        if($pesaKor!=$khat_fees){



            $bokeya = $khat_fees - $pesaKor;

            $results->update(['bokeya'=>$bokeya]);
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

            if($results->sonod_name=='ট্রেড লাইসেন্স'){
                $results->renew_able = ($isNotCurrentOrthoBchor && $isRenewable);
            }else{
                $results->renew_able = false;
            }


            // Initialize download URLs
            $results->bokeya_payment_url = '';
            $results->download_url = '';
            $results->download_url_en = '';


            if($results->bokeya > 0){
                // $url = url('/bokeya/payment');
                // Generate bokeya payment URL
                $bokeyaPaymentUrl = asset("/create/payment?sonod_id=$results->id");
                $results->bokeya_payment_url = $bokeyaPaymentUrl;
            }




            // Check if the Sonod is approved and paid
            if ($results->stutus === 'approved' && $results->payment_status === 'Paid' &&           (
                    $results->bokeya === null ||
                    $results->bokeya === 0 ||
                    $results->bokeya === '0' ||
                    $results->bokeya <= 1
                )) {
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
        $hasEnData = $request->query('hasEnData') ?? 0; // Get hasEnData from the URL query string
        $uddoktaId = $request->query('uddoktaId'); // Get uddoktaId from the URL query string
        $only_english = $request->query('only_english');

        // Prepare the URLs array
        $urls = [
            "s_uri" => $sUri,
            "f_uri" => $fUri,
            "c_uri" => $cUri,
        ];

        // Generate the redirect URL using the sonodpayment function
        $redirectUrl = sonodpayment($sonodId, $urls, $hasEnData, $uddoktaId,$only_english);


        return redirect($redirectUrl);
        // Return the redirect URL (or use it as needed)
        return response()->json($redirectUrl);
    }




}
