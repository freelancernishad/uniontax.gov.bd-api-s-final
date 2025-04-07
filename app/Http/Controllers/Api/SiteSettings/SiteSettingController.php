<?php

namespace App\Http\Controllers\Api\SiteSettings;

use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class SiteSettingController extends Controller
{
    /**
     * Store or update site settings.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrUpdate(Request $request)
    {
        // Validation rules for the input
        $rules = [
            '*' => 'required|array', // Each item must be an array
            '*.key' => 'required|string', // Each key must be a string
            '*.value' => 'required|string', // Each value must be a string
        ];

        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Retrieve settings from the request
        $settingsData = $request->all();

        foreach ($settingsData as $setting) {
            // Update or create the setting in the database
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        // Return success response
        return response()->json([
            'message' => 'Site settings saved successfully!',
        ], 200);
    }

    /**
     * Get the list of all site settings.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList()
    {
        // Retrieve all site settings from the database
        $siteSettings = SiteSetting::all();

        return response()->json($siteSettings, 200);
    }

}
