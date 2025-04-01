<?php

namespace App\Http\Controllers\Api\Global;

use App\Models\Sonod;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use App\Models\Sonodnamelist;
use App\Models\TradeLicenseKhat;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

        // Handle TradeLicenseKhat type
        if ($type == 'TradeLicenseKhat') {
            $TradeLicenseKhat = TradeLicenseKhat::with('khatFees.khat2')
                ->where('main_khat_id', 0) // Fetch only parent records (where main_khat_id is 0)
                ->select('name', 'khat_id', 'main_khat_id')
                ->get()
                ->map(function ($khat) {
                    // Check if the TradeLicenseKhat has any child records
                    $hasChild = TradeLicenseKhat::where('main_khat_id', $khat->khat_id)->exists();

                    return [
                        'name' => $khat->name,
                        'khat_id' => $khat->khat_id,
                        'main_khat_id' => $khat->main_khat_id,
                        'has_child' => $hasChild, // Add this column
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

            return response()->json($TradeLicenseKhat, 200);
        }


        // Handle Uniouninfo and Sonodnamelist
        $auth = Auth::user();
        $shortName = $request->query('name');
        if ($auth) {
            $shortName = $auth->unioun;
        }

        if (!$shortName) {
            return response()->json(['error' => 'short_name_e is required'], 400);
        }

        $columns = ['id', 'short_name_e', 'short_name_b', 'thana', 'district', 'web_logo', 'format', 'google_map', 'defaultColor', 'payment_type', 'nidServicestatus', 'nidService', 'u_image', 'u_description', 'u_notice'];

        // Fetch Uniouninfo data
        $uniouninfos = Uniouninfo::with('postOffices')->where('short_name_e', $shortName)
            ->select($columns)
            ->first();

        if (!$uniouninfos) {
            return response()->json(['message' => 'No data found'], 404);
        }

        // Replace file path columns with full URLs using the /files/{path} route
        $fileFields = ['web_logo', 'sonod_logo', 'c_signture', 'socib_signture', 'u_image'];

        foreach ($fileFields as $field) {
            if ($uniouninfos->$field) {
                try {
                    // Replace the file path with the full URL
                    $uniouninfos->$field = URL::to('/files/' . $uniouninfos->$field);
                } catch (\Exception $e) {
                    // If the file is not found or cannot be read, set the value to null
                    $uniouninfos->$field = null;
                }
            } else {
                // If the field is empty, set the value to null
                $uniouninfos->$field = null;
            }
        }

        // Fetch Sonodnamelist data
        $sonod_name_lists = Sonodnamelist::select(['id', 'service_id', 'bnname', 'enname', 'icon'])
            ->with(['sonodFees' => function ($query) use ($shortName, $auth) {
                $query->select('service_id', 'fees')
                    ->where('unioun', $shortName);
            }])
            ->get()
            ->map(function ($sonod) use ($shortName, $auth) {
                if ($auth) {
                    $stutus = 'Pending';
                    if ($auth->position == 'Chairman') {
                        $stutus = 'sec_approved';
                    }
                    // Fetch the count of Sonod statuses
                    $pendingCount = Sonod::where('unioun_name', $shortName)
                        ->where('sonod_name', $sonod->bnname)
                        ->where('stutus', $stutus)
                        ->count();
                } else {
                    $pendingCount = 0;
                }

                // Collect Sonod fees
                $fees = $sonod->sonodFees->pluck('fees')->implode(', ');

                return [
                    'id' => $sonod->id,
                    'bnname' => $sonod->bnname,
                    'enname' => $sonod->enname,
                    'icon' => $sonod->icon,
                    'sonod_fees' => $fees ? (int)$fees : 0,
                    'pendingCount' => $pendingCount,
                ];
            });

        $returnData = [
            'uniouninfos' => $uniouninfos,
            'sonod_name_lists' => $sonod_name_lists,
            // 'villages' => $uniouninfos->villages,
        ];

        return response()->json($returnData, 200);
    }


}
