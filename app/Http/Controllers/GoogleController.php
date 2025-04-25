<?php

namespace App\Http\Controllers;

use Google_Client;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Google\Service\PeopleService as Google_Service_PeopleService;
use Google\Service\PeopleService\Name as Google_Service_PeopleService_Name;
use Google\Service\PeopleService\PhoneNumber as Google_Service_PeopleService_PhoneNumber;
use Google\Service\Oauth2 as Google_Service_Oauth2;
use Google\Service\PeopleService\Person as Google_Service_PeopleService_Person;

class GoogleController extends Controller
{
    // Step 1: Redirect to Google for authentication
    public function redirectToGoogle()
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(route('google.callback'));
        $client->setScopes([
            Google_Service_PeopleService::CONTACTS_READONLY,
            'email',
            'profile',
            'https://www.googleapis.com/auth/contacts'
        ]);
        $client->setAccessType('offline');  // Request refresh token

        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    // Step 2: Handle Google callback and store refresh token
    public function handleGoogleCallback(Request $request)
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(route('google.callback'));

        // Request offline access for refresh tokens
        $client->setAccessType('offline');
        $client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);
        $client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
        $client->addScope('https://www.googleapis.com/auth/contacts.readonly');
        $client->addScope('https://www.googleapis.com/auth/contacts');

        if ($request->has('code')) {
            // Exchange the authorization code for access and refresh tokens
            try {
                $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));

                // If there's an error in the token request, log it and return
                if (isset($token['error'])) {
                    Log::error('Google OAuth Error: ' . $token['error_description']);
                    return redirect('/')->with('error', 'Failed to get access token: ' . $token['error_description']);
                }

                // Log the entire token response to verify the structure
                Log::info('Received Token Response: ' . json_encode($token));

                // Check for the refresh token
                $refreshToken = $token['refresh_token'] ?? null;

                // Log the refresh token to check if it's being correctly received
                Log::info('Refresh Token: ' . ($refreshToken ? $refreshToken : 'No refresh token returned'));

                if ($refreshToken) {
                    // Attempt to find or create the 'google_refresh_token' entry
                    $systemSetting = SystemSetting::updateOrCreate(
                        ['key' => 'google_refresh_token'],
                        ['value' => encrypt($refreshToken)]  // Encrypt token for storage
                    );
                }

                return redirect('/')->with('success', 'Successfully connected to Google!');
            } catch (\Exception $e) {
                // Catch any exceptions during the process and log the error
                Log::error('Google OAuth Exception: ' . $e->getMessage());
                return redirect('/')->with('error', 'An error occurred during the authentication process.');
            }
        }

        return redirect('/')->with('error', 'Google login failed');
    }

    // Step 3: Get contacts and check if a phone number exists in the Google account
    public function checkPhoneNumberInGoogleContacts(Request $request, $phoneNumber)
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));

        $refreshToken = SystemSetting::where('key', 'google_refresh_token')->value('value');
        // Decrypt the refresh token from the database
        $refreshToken = decrypt($refreshToken);

        if (!$refreshToken) {
            return response()->json(['error' => 'No refresh token found. Please authenticate first.'], 401);
        }

        // Use the refresh token to get a new access token
        $client->refreshToken($refreshToken);
        $accessToken = $client->getAccessToken();

        // Create the Google People Service instance
        $service = new Google_Service_PeopleService($client);
        $optParams = [
            'pageSize' => 2000,
            'personFields' => 'names,emailAddresses,phoneNumbers',
        ];

        try {
            // Get connections (contacts) from Google account
            $connections = $service->people_connections->listPeopleConnections('people/me', $optParams);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch contacts from Google: ' . $e->getMessage()], 500);
        }

        $contacts = [];
        $isPhoneNumberFound = false;

        // Search through the contacts for the given phone number
        foreach ($connections->getConnections() as $person) {
            if ($person->getPhoneNumbers()) {
                foreach ($person->getPhoneNumbers() as $phone) {
                    if ($phone->getValue() === $phoneNumber) {
                        $isPhoneNumberFound = true;
                        break 2;  // Exit both loops when phone number is found
                    }
                }
            }
        }

        if ($isPhoneNumberFound) {
            return response()->json(['message' => 'Phone number exists in Google contacts'], 200);
        }

        return response()->json(['message' => 'Phone number does not exist in Google contacts'], 404);
    }

    // Step 4: Get contacts using the refresh token
    public function getGoogleContacts()
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));

        // Retrieve the refresh token from the database
        $refreshToken = SystemSetting::where('key', 'google_refresh_token')->value('value');

        if (!$refreshToken) {
            return response()->json(['error' => 'No refresh token found. Please authenticate first.'], 401);
        }

        // Decrypt the refresh token from the database
        $refreshToken = decrypt($refreshToken);

        // Use the refresh token to get a new access token
        $client->refreshToken($refreshToken);
        $accessToken = $client->getAccessToken();

        // Use the access token to make API calls
        $service = new Google_Service_PeopleService($client);
        $optParams = [
            'pageSize' => 2000,
            'personFields' => 'names,emailAddresses,phoneNumbers',
        ];

        try {
            $connections = $service->people_connections->listPeopleConnections('people/me', $optParams);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }

        $contacts = [];

        foreach ($connections->getConnections() as $person) {
            $name = '';
            $email = '';
            $phone = '';

            // Extract name
            if ($person->getNames() && count($person->getNames()) > 0) {
                $name = $person->getNames()[0]->getDisplayName();
            }

            // Extract email
            if ($person->getEmailAddresses() && count($person->getEmailAddresses()) > 0) {
                $email = $person->getEmailAddresses()[0]->getValue();
            }

            // Extract phone number
            if ($person->getPhoneNumbers() && count($person->getPhoneNumbers()) > 0) {
                $phone = $person->getPhoneNumbers()[0]->getValue();
            }

            if ($email || $phone) {
                $contacts[] = compact('name', 'email', 'phone');
            }
        }

        return response()->json($contacts);
    }

    public function addOrUpdateContacts(Request $request)
    {
        // Validate the input for multiple contacts
        $validator = Validator::make($request->all(), [
            'contacts' => ['required', 'array'],
            'contacts.*.name' => ['required', 'string', 'max:255'],
            'contacts.*.phone_number' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]+$/'],
        ]);
    
        Log::info('Validation Errors: ' . json_encode($validator->errors()));
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
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
    }
    
    
    
    
    
    
}
