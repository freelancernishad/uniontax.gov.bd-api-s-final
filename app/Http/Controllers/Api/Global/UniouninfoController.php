<?php

namespace App\Http\Controllers\Api\Global;

use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use App\Models\TradeLicenseKhat;
use App\Http\Controllers\Controller;
use App\Models\Sonodnamelist;

class UniouninfoController extends Controller
{
    /**
     * Get Uniouninfos by short_name_e.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByShortName(Request $request)
    {
        // Get the type from the request query
        $type = $request->query('type');

        // Cache the result for TradeLicenseKhat
        if ($type == 'TradeLicenseKhat') {
            $cacheKey = 'TradeLicenseKhat_all';

            // Attempt to get data from the cache
            $TradeLicenseKhat = cache()->remember($cacheKey, 60, function () {
                return TradeLicenseKhat::with('khatFees.khat2')->where('main_khat_id', 0)
                    ->select('name', 'khat_id')
                    ->get()
                    ->map(function ($khat) {
                        return [
                            'name' => $khat->name,
                            'khat_id' => $khat->khat_id,
                            'khat_fees' => $khat->khatFees->map(function ($fee) {
                                return [
                                    'name' => $fee->khat2->name ?? null,
                                    'applicant_type_of_businessKhat' => $fee->khat_id_1,
                                    'applicant_type_of_businessKhatAmount' => $fee->khat_id_2,
                                    'fee' => $fee->fee,
                                ];
                            })
                        ];
                    });
            });

            return response()->json($TradeLicenseKhat, 200);
        }

        // Cache the result for Uniouninfo and Sonodnamelist
        $shortName = $request->query('name');

        if (!$shortName) {
            return response()->json(['error' => 'short_name_e is required'], 400);
        }

        $columns = ['id', 'short_name_e', 'short_name_b', 'thana', 'district', 'web_logo', 'format', 'google_map', 'defaultColor', 'payment_type', 'nidServicestatus', 'nidService', 'u_image', 'u_description', 'u_notice'];

        // Cache the uniouninfos data
        $uniounCacheKey = 'uniouninfo_' . $shortName;
        $uniouninfos = cache()->remember($uniounCacheKey, 60, function () use ($shortName, $columns) {
            return Uniouninfo::where('short_name_e', $shortName)
                ->select($columns)
                ->first();
        });

        if (!$uniouninfos) {
            return response()->json(['message' => 'No data found'], 404);
        }

        // Cache the sonod_name_lists data
        $sonodCacheKey = 'sonod_name_lists_' . $shortName;
        $sonod_name_lists = cache()->remember($sonodCacheKey, 60, function () use ($shortName) {
            return Sonodnamelist::select(['id', 'service_id', 'bnname', 'enname', 'icon'])
                ->with(['sonodFees' => function ($query) use ($shortName) {
                    $query->select('service_id', 'fees')
                          ->where('unioun', $shortName);
                }])
                ->get()
                ->map(function ($sonod) {
                    $fees = $sonod->sonodFees->pluck('fees')->implode(', ');
                    return [
                        'id' => $sonod->id,
                        'bnname' => $sonod->bnname,
                        'enname' => $sonod->enname,
                        'icon' => $sonod->icon,
                        'sonod_fees' => $fees ? (int)$fees : 0
                    ];
                });
        });

        $returnData = [
            'uniouninfos' => $uniouninfos,
            'sonod_name_lists' => $sonod_name_lists,
        ];

        return response()->json($returnData, 200);
    }

}
