<?php

namespace App\Http\Controllers;

use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Devfaysal\BangladeshGeocode\Models\Upazila;

class VercelController extends Controller
{
    // Add the addDomainToVercel function if it's not already here
    public function addDomainToVercel(Request $request)
    {
        $request->validate([
            'domain' => 'required|url',
        ]);

        $projectId = 'prj_1hTOq9QsiazXq4m213FOBcPc3zTg'; // Your Vercel Project ID
        $apiToken = 'BhVmO2fRpmXh4ZtGjhtQ1Y3d'; // Your Vercel API Token

        $data = [
            'name' => $request->domain,
            'redirect' => null,
            'redirectStatusCode' => 307,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiToken,
            'Content-Type' => 'application/json',
        ])
        ->post("https://api.vercel.com/v10/projects/{$projectId}/domains", $data);

        if ($response->successful()) {
            return response()->json([
                'message' => 'Domain added successfully!',
                'domain' => $response->json(),
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to add domain.',
                'error' => $response->json(),
            ], $response->status());
        }
    }

    /**
     * Loop through all Unioninfo records and create domains for each.
     *
     * @return \Illuminate\Http\Response
     */
    public function createDomainsForAllUniouninfo()
{
    // Fetch all records from the Uniouninfo model
    $uniounInfos = Uniouninfo::all();

    // Prepare responses
    $responses = [];

    foreach ($uniounInfos as $uniounInfo) {
        // Check if short_name_e is present
        if ($uniounInfo->short_name_e) {
            $domain1 = $uniounInfo->short_name_e . '.unionservices.gov.bd';
            $domain2 = $uniounInfo->short_name_e . '.uniontax.gov.bd';

            // Call the addDomainToVercel method for the first domain
            $response1 = $this->addDomainToVercelForSpecificDomain($domain1);

            // Optional: Sleep for 1 second to introduce a delay between the requests
            sleep(1);  // Delay for 1 second, you can adjust the time as needed

            // Call the addDomainToVercel method for the second domain
            $response2 = $this->addDomainToVercelForSpecificDomain($domain2);

            // Optional: Log or return responses
            $responses[] = [
                'unioun_name' => $uniounInfo->short_name_e,
                'domains' => [
                    'unionservices' => $response1,
                    'uniontax' => $response2
                ]
            ];
        }
    }

    return response()->json($responses);
}

private function addDomainToVercelForSpecificDomain($domain)
    {
        $projectId = 'prj_1hTOq9QsiazXq4m213FOBcPc3zTg'; // Your Vercel Project ID
        $apiToken = 'BhVmO2fRpmXh4ZtGjhtQ1Y3d'; // Your Vercel API Token

        $data = [
            'name' => $domain,
            'redirect' => null,
            'redirectStatusCode' => 307,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiToken,
            'Content-Type' => 'application/json',
        ])
        ->post("https://api.vercel.com/v10/projects/{$projectId}/domains", $data);

        if ($response->successful()) {
            return [
                'status' => 'success',
                'domain' => $domain,
                'response' => $response->json()
            ];
        } else {
            return [
                'status' => 'failed',
                'domain' => $domain,
                'error' => $response->json(),
                'status_code' => $response->status()
            ];
        }
    }

    public function getVercelPromotedAliases()
    {
        $projectId = 'prj_1hTOq9QsiazXq4m213FOBcPc3zTg'; // Your Vercel Project ID
        $apiToken = 'BhVmO2fRpmXh4ZtGjhtQ1Y3d'; // Your Vercel API Token
        $baseUrl = "https://api.vercel.com/v1/projects/$projectId/promote/aliases";

        $allAliases = [];
        $nextPage = null;

        do {
            // Construct the URL (add pagination parameter if needed)
            $url = $baseUrl;
            if ($nextPage) {
                $url .= "?until=$nextPage";
            }

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . $apiToken,
                    "Content-Type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                return response()->json(['error' => $err], 500);
            }

            $data = json_decode($response, true);

            // Check if valid data exists
            if (isset($data['aliases']) && is_array($data['aliases'])) {
                $allAliases = array_merge($allAliases, $data['aliases']);
            }

            // Check if there's a next page
            $nextPage = $data['pagination']['next'] ?? null;

        } while ($nextPage); // Continue until there are no more pages

        return response()->json($allAliases);
    }






    public function deleteVercelDomain($domain)
    {
        $projectId = 'prj_1hTOq9QsiazXq4m213FOBcPc3zTg'; // Your Vercel Project ID
        $apiToken = 'BhVmO2fRpmXh4ZtGjhtQ1Y3d'; // Your Vercel API Token
        $url = "https://api.vercel.com/v9/projects/$projectId/domains/$domain";

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $apiToken,
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json(['error' => $err], 500);
        } else {
            return response()->json(json_decode($response, true));
        }
    }



    public function deleteSubdomainsFromVercel()
{
    $projectId = 'prj_1hTOq9QsiazXq4m213FOBcPc3zTg'; // Your Vercel Project ID
    $apiToken = 'BhVmO2fRpmXh4ZtGjhtQ1Y3d'; // Your Vercel API Token
    $baseUrl = "https://api.vercel.com/v1/projects/$projectId/promote/aliases";

    $allAliases = [];
    $nextPage = null;

    // Step 1: Fetch all aliases
    do {
        $url = $baseUrl;
        if ($nextPage) {
            $url .= "?until=$nextPage";
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $apiToken,
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return response()->json(['error' => $err], 500);
        }

        $data = json_decode($response, true);

        if (isset($data['aliases']) && is_array($data['aliases'])) {
            $allAliases = array_merge($allAliases, $data['aliases']);
        }

        $nextPage = $data['pagination']['next'] ?? null;

    } while ($nextPage);

    // Step 2: Filter out subdomains (e.g., keep only "sub.example.com", ignore "example.com")
    $subdomains = [];
    foreach ($allAliases as $alias) {
        $domain = $alias['alias'] ?? null;
        if ($domain && substr_count($domain, '.') >= 2) { // Subdomain check: at least 2 dots (sub.example.com)
            $subdomains[] = $domain;
        }
    }

    // Step 3: Delete each subdomain
    $deleted = [];
    $errors = [];

    foreach ($subdomains as $domain) {
        $deleteUrl = "https://api.vercel.com/v9/projects/$projectId/domains/$domain";

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $deleteUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $apiToken,
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $errors[] = ['domain' => $domain, 'error' => $err];
        } else {
            $deleted[] = ['domain' => $domain, 'response' => json_decode($response, true)];
        }
    }

    // Step 4: Return response summary
    return response()->json([
        'deleted' => $deleted,
        'errors' => $errors
    ]);
}




public function createDomainsByUpazila($upazilaId)
{
    // Fetch the Upazila with its related unions and district/division data
    $Upazila = Upazila::find($upazilaId);

    if (!$Upazila) {
        return response()->json([
            'message' => 'Upazila not found',
        ], 404);
    }

    // Fetch all Uniouninfo entries that belong to this Upazila
    $uniounInfos = Uniouninfo::select('short_name_e','thana')->where('thana', $Upazila->bn_name)->get();
    // return response()->json($uniounInfos);
    // Prepare responses
    $responses = [];

    foreach ($uniounInfos as $uniounInfo) {
        // Check if short_name_e exists and is valid
        if ($uniounInfo->short_name_e) {
            $domain1 = $uniounInfo->short_name_e . '.unionservices.gov.bd';
            $domain2 = $uniounInfo->short_name_e . '.uniontax.gov.bd';

            // Call the addDomainToVercel method for both domains
            // $response1 = $this->addDomainToVercelForSpecificDomain($domain1);
            $response1 = $this->deleteVercelDomain($domain1);

            // Optional delay to prevent hitting Vercel API rate limits
            // sleep(1);

            // $response2 = $this->addDomainToVercelForSpecificDomain($domain2);
            $response2 = $this->deleteVercelDomain($domain2);

            // Collect the responses
            $responses[] = [
                'union_name' => $uniounInfo->short_name_e,
                'domains' => [
                    'unionservices' => $response1,
                    'uniontax' => $response2
                ]
            ];
        }
    }

    // Return final response
    return response()->json([
        'upazila' => $Upazila->name,
        'unions' => $responses,
        'message' => 'Domains created successfully for all unions under this Upazila.'
    ], 201);
}




}
