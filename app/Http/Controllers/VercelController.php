<?php

namespace App\Http\Controllers;

use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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
                // Log::info($domain1);
                // Log::info($domain2);

                // Call the addDomainToVercel method for both domains
                $response1 = addDomainToVercelForSpecificDomain($domain1);
                $response2 = addDomainToVercelForSpecificDomain($domain2);

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


}
