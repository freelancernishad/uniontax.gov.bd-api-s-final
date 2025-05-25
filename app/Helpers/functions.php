<?php

use Carbon\Carbon;
use App\Models\SiteSetting;
use App\Models\BkashPayment;
use App\Models\MaintanceFee;
use App\Models\SystemSetting;
use App\Models\TokenBlacklist;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


// Removed redundant use statement for Google_Client
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Google\Service\Oauth2 as Google_Service_Oauth2;
use Google\Service\PeopleService as Google_Service_PeopleService;
use Google\Service\PeopleService\Name as Google_Service_PeopleService_Name;
use Google\Service\PeopleService\Person as Google_Service_PeopleService_Person;
use Google\Service\PeopleService\PhoneNumber as Google_Service_PeopleService_PhoneNumber;



function isUnion(){
    $isUnion = SiteSetting::where('key', 'union')->first()->value;
    Log::info('isUnion: ' . $isUnion);

    return filter_var($isUnion, FILTER_VALIDATE_BOOLEAN);
}

function siteSetting($key=null){
    $siteSetting = SiteSetting::where('key', $key)->first()->value;
    return $siteSetting;
}


function TokenBlacklist($token){
// Get the authenticated user for each guard
    $user = null;
    $guardType = null;

    if (Auth::guard('admin')->check()) {
        $user = Auth::guard('admin')->user();
        $guardType = 'admin';
    } elseif (Auth::guard('user')->check()) {
        $user = Auth::guard('user')->user();
        $guardType = 'user';
    }


    TokenBlacklist::create([
            'token' => $token,
            'user_id' => $user->id,
            'user_type' => $guardType,
            'date' => Carbon::now(),
            ]);
}



function validateRequest(array $data, array $rules)
{
    $validator = Validator::make($data, $rules);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    return null; // Return null if validation passes
}


function int_en_to_bn($number)
{
    $bn_digits = array('০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯');
    $en_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

    return str_replace($en_digits, $bn_digits, $number);
}
function int_bn_to_en($number)
{

    $bn_digits = array('০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯');
    $en_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

    return str_replace($bn_digits, $en_digits, $number);
}



function getBanglaPositionText($position)
{
    $positionMap = [
        'District_admin' => 'জেলা প্রশাসকের ড্যাশবোর্ড',
        'DLG' => 'পরিচালক, (যুগ্মসচিব) স্থানীয় সরকার',
        'super_admin' => 'সুপার এডমিনের ড্যাশবোর্ড',
        'Sub_District_admin' => 'উপ-পরিচালকের ড্যাশবোর্ড',
        'Chairman' => isUnion() ? 'চেয়ারম্যানের ড্যাশবোর্ড' : 'প্রশাসকের ড্যাশবোর্ড',
        'Secretary' => 'প্রশাসনিক কর্মকর্তার ড্যাশবোর্ড',
    ];

    // Return the Bangla text for the position, or a default value if not found
    return $positionMap[$position] ?? 'উপজেলা ড্যাশবোর্ড';
}


 function getBanglaDesignationText($position)
{
    $designationMap = [
        'District_admin' => 'জেলা প্রশাসক',
        'DLG' => 'পরিচালক, (যুগ্মসচিব) স্থানীয় সরকার',
        'super_admin' => 'সুপার এডমিন',
        'Sub_District_admin' => 'উপ-পরিচালক',
        'Chairman' => isUnion() ? 'চেয়ারম্যান' : 'প্রশাসক',
        'Secretary' => 'প্রশাসনিক কর্মকর্তা',
    ];

    // Return the Bangla text for the designation, or a default value if not found
    return $designationMap[$position] ?? 'উপজেলা কর্মকর্তা';
}






function addOrUpdateContacts(array $contactsArray)
{
    try {
        // Validate the input for multiple contacts
        $validator = Validator::make(['contacts' => $contactsArray], [
            'contacts' => ['required', 'array'],
            'contacts.*.name' => ['required', 'string', 'max:255'],
            'contacts.*.phone_number' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]+$/'],
        ]);

        Log::info('Validation Errors: ' . json_encode($validator->errors()));
        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $validated = $validator->validated();

        $contacts = $validated['contacts']; // Array of contacts

        // Retrieve the refresh token from the database
        $refreshToken = SystemSetting::where('key', 'google_refresh_token')->value('value');

        // Decrypt the refresh token from the database
        $refreshToken = decrypt($refreshToken);

        if (!$refreshToken) {
            Log::error('No refresh token found.');
            return response()->json(['error' => 'No refresh token found. Please authenticate first.'], 401);
        }

        // Initialize the Google Client
        $client = new Google_Client();
        $client->setClientId(config('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(config('GOOGLE_CLIENT_SECRET'));
        $client->refreshToken($refreshToken);
        $accessToken = $client->getAccessToken();
        $service = new Google_Service_PeopleService($client);

        $responses = []; // Array to collect responses for all contacts

        try {
            // Loop through each contact and add or update it
            foreach ($contacts as $contactData) {
                $name = $contactData['name'];
                $phoneNumber = $contactData['phone_number'];

                // Check if the phone number exists in the contacts
                $optParams = [
                    'pageSize' => 2000,
                    'personFields' => 'phoneNumbers,names',
                ];

                // Fetch connections (contacts) from Google
                $connections = $service->people_connections->listPeopleConnections('people/me', $optParams);

                $existingContact = null;
                $contactResourceName = null;

                // Loop through connections and check if the phone number exists
                foreach ($connections->getConnections() as $person) {
                    if ($person->getPhoneNumbers()) {
                        foreach ($person->getPhoneNumbers() as $phone) {
                            if ($phone->getValue() === $phoneNumber) {
                                $existingContact = $person;
                                $contactResourceName = $person->getResourceName(); // Save the resourceName for update
                                break 2;  // Exit both loops when phone number is found
                            }
                        }
                    }
                }

                if ($existingContact) {
                    // Update the name of the existing contact
                    $updatedNameObj = new Google_Service_PeopleService_Name();
                    $updatedNameObj->setGivenName($name);

                    // Set the updated name for the existing contact
                    $existingContact->setNames([$updatedNameObj]);

                    // Update the contact using the correct resource name
                    $updateMask = 'names'; // Specify that we are updating the name field
                    $updatedContact = $service->people->updateContact($contactResourceName, $existingContact, ['updatePersonFields' => $updateMask]);

                    $responses[] = [
                        'message' => 'Contact name updated successfully',
                        'contact' => [
                            'name' => $updatedContact->getNames()[0]->getGivenName(),
                            'phone_number' => $phoneNumber,
                        ]
                    ];
                } else {
                    // If no existing contact found, create a new one
                    $newContact = new Google_Service_PeopleService_Person();

                    $nameObj = new Google_Service_PeopleService_Name();
                    $nameObj->setGivenName($name);
                    $newContact->setNames([$nameObj]);

                    $phoneObj = new Google_Service_PeopleService_PhoneNumber();
                    $phoneObj->setValue($phoneNumber);
                    $newContact->setPhoneNumbers([$phoneObj]);

                    // Add the new contact to Google Contacts
                    $createdContact = $service->people->createContact($newContact);

                    // Return name and phone number for the newly created contact
                    $responses[] = [
                        'message' => 'Contact added successfully',
                        'contact' => [
                            'name' => $createdContact->getNames()[0]->getGivenName(),
                            'phone_number' => $phoneNumber,
                        ]
                    ];
                }
            }

            return response()->json($responses);

        } catch (\Exception $e) {
            Log::error('Google Contacts API Error: ' . $e->getMessage());
            return response()->json(['error' => 'Error while adding/updating contacts: ' . $e->getMessage()], 500);
        }
    } catch (\Exception $e) {
        Log::error('Error in addOrUpdateContacts: ' . $e->getMessage());
        return response()->json(['error' => 'Error in addOrUpdateContacts: ' . $e->getMessage()], 500);
    }
}


function convertToMySQLDate($date) {
    // Convert date format "08/25/2001" to "Y-m-d"
    $timestamp = strtotime($date);
    return $timestamp ? date('Y-m-d', $timestamp) : null;
}








function generatePaymentUrl($amount, $payerReference = "01700000000", $callbackURL = "https://yourdomain.com/callback")
{


        $baseUrl = env('BKASH_BASE_URL');
        $appKey = env('BKASH_APP_KEY');
        $appSecret = env('BKASH_APP_SECRET');
        $username = env('BKASH_USERNAME');
        $password = env('BKASH_PASSWORD');


    // Step 1: Get token
    $tokenResponse = Http::withHeaders([
        'Content-Type' => 'application/json',
        'username' => $username,
        'password' => $password
    ])->post("$baseUrl/token/grant", [
        'app_key' => $appKey,
        'app_secret' => $appSecret
    ]);

    if (!$tokenResponse->successful()) {
        return response()->json(['error' => 'Failed to get token', 'details' => $tokenResponse->body()], 500);
    }

    $token = $tokenResponse->json()['id_token'];

    // Step 2: Create payment
    $invoice = uniqid('INV-');
    $paymentResponse = Http::withToken($token)
        ->withHeaders([
            'Content-Type' => 'application/json',
            'X-APP-Key' => $appKey
        ])
        ->post("$baseUrl/create", [
            "mode" => "0011",
            "payerReference" => $payerReference,
            "callbackURL" => $callbackURL,
            "amount" => $amount,
            "currency" => "BDT",
            "intent" => "sale",
            "merchantInvoiceNumber" => $invoice
        ]);

    if (!$paymentResponse->successful()) {
        return response()->json(['error' => 'Failed to create payment', 'details' => $paymentResponse->body()], 500);
    }

    $paymentData = $paymentResponse->json();
    Log::info('Payment Data: ' . json_encode($paymentData));


    // ✅ Save to database
    BkashPayment::create([
        'id_token' => $token,
        'payment_id' => $paymentData['paymentID'],
        'amount' => $amount,
        'invoice' => $invoice,
        'status' => 'initiated'
    ]);

    return $paymentData;
}


    function getHasPaidMaintanceFee($union,$type="monthly")
    {

           if ($type === 'Free Trial') {
                return true;
            }
        // return $type;
        if ($type === 'yearly') {
            $period = CurrentOrthoBochor(); // implement below
        } else {
            $period = now()->format('Y-m');
        }

        return MaintanceFee::where('union', $union)
            ->where('type', $type)
            ->where('period', $period)
            ->where('status', 'paid')
            ->exists();
    }

