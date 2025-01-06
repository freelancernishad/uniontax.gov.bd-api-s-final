<?php

namespace App\Http\Controllers\Api\Auth\Uddokta;

use Illuminate\Http\Request;
use App\Models\UddoktaSearch;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class CitizenInformationController extends Controller
{
    /**
     * Base URL for the API.
     *
     * @var string
     */
    private $baseUrl;

    /**
     * Constructor to initialize the base URL.
     */
    public function __construct()
    {
        $this->baseUrl = 'https://uniontax.xyz/api';
    }

    /**
     * Generate sToken by calling the token generation API.
     *
     * @return string|null
     */
    private function generateSToken()
    {
        try {
            // Call the token generation API
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->GET($this->baseUrl . '/token/genarate');

            // Log the full response for debugging
            \Log::info('API Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            // Check if the request was successful
            if ($response->successful()) {
                // Extract the apitoken from the response
                $data = $response->json();
                if (isset($data['apitoken'])) {
                    return $data['apitoken']; // Use 'apitoken' instead of 'sToken'
                } else {
                    // Handle cases where the response does not contain the apitoken
                    \Log::error('apitoken not found in API response', ['response' => $data]);
                    return null;
                }
            } else {
                // Handle API errors
                $statusCode = $response->status();
                $errorDetails = $response->json();
                \Log::error('Failed to generate apitoken', [
                    'status' => $statusCode,
                    'details' => $errorDetails,
                ]);
                return null;
            }
        } catch (\Exception $e) {
            // Handle exceptions (e.g., network errors)
            \Log::error('Exception while generating apitoken', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Make an API call with the specified method, endpoint, and body.
     *
     * @param string $method The HTTP method (e.g., 'GET', 'POST', 'PUT', 'DELETE').
     * @param string $endpoint The API endpoint URL.
     * @param array $body The request body (for POST, PUT, etc.).
     * @return \Illuminate\Http\JsonResponse
     */
    private function makeApiCall($method, $endpoint, $body = [])
    {
        // Validate the method
        $method = strtoupper($method);
        if (!in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])) {
            return response()->json(['error' => 'Invalid HTTP method'], 400);
        }

        // Make the API call using Laravel's Http facade
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer YOUR_ACCESS_TOKEN', // Add your token if needed
            ])->$method($this->baseUrl . $endpoint, $body);

            // Check if the request was successful
            if ($response->successful()) {
                // Return the API response
                return $response->json();
                // return response()->json($response->json(), $response->status());
            } else {
                // Handle API errors
                return response()->json(['error' => 'API request failed', 'details' => $response->json()], $response->status());
            }
        } catch (\Exception $e) {
            // Handle exceptions (e.g., network errors)
            return response()->json(['error' => 'API call failed', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch citizen information using NID.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function citizeninformationNID(Request $request)
    {
        // Generate sToken
        $sToken = $this->generateSToken();
        if (!$sToken) {
            return response()->json(['error' => 'Failed to generate sToken. Please try again later.'], 500);
        }

        // Validate the request data
        $request->validate([
            'nidNumber' => 'required|string',
            'dateOfBirth' => 'required|date',
            'sonod_name' => 'required|string', // Add sonod_name to validation
        ]);

        // Get the authenticated uddokta
        $uddokta = auth('uddokta')->user();

        // Check if the uddokta has any existing search record
        $existingSearch = UddoktaSearch::where('uddokta_id', $uddokta->id)->first();

        if ($existingSearch) {
            return response()->json(['error' => 'You already have an existing search record. Please complete that application first.'], 403);
        }

        // Prepare the request body
        $body = [
            "nidNumber" => $request->nidNumber,
            "dateOfBirth" => $request->dateOfBirth,
            "sToken" => $sToken,
        ];

        // API endpoint for NID
        $endpoint = "/citizen/information/nid";

        // Make the API call using POST method
        $apiResponse = $this->makeApiCall('POST', $endpoint, $body);



        // Store the search data and API response temporarily
        UddoktaSearch::create([
            'sonod_name' => $request->sonod_name,
            'nid_number' => $request->nidNumber,
            'uddokta_id' => $uddokta->id,
            'api_response' => json_encode($apiResponse), // Store the API response
        ]);

        // Return the API response to the client
        return response()->json($apiResponse);
    }



    /**
     * Fetch citizen information using BRN.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function citizeninformationBRN(Request $request)
    {
        // Generate sToken
        $sToken = $this->generateSToken();
        if (!$sToken) {
            return response()->json(['error' => 'Failed to generate sToken. Please try again later.'], 500);
        }

        // Validate the request data
        $request->validate([
            'nidNumber' => 'required|string',
            'dateOfBirth' => 'required|date',
        ]);

        // Prepare the request body
        $body = [
            "nidNumber" => $request->nidNumber,
            "dateOfBirth" => $request->dateOfBirth,
            "sToken" => $sToken,
        ];

        // API endpoint for BRN
        $endpoint = "/citizen/information/brn";

        // Make the API call using POST method
        return $this->makeApiCall('POST', $endpoint, $body);
    }
}
