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


        $type = $request->query('type');
        if($type=='TradeLicenseKhat'){
            $TradeLicenseKhat =  TradeLicenseKhat::with('khatFees.khat2')->get();
            return response()->json($TradeLicenseKhat, 200);
        }


        $shortName = $request->query('name');

        if (!$shortName) {
            return response()->json(['error' => 'short_name_e is required'], 400);
        }

        $columns = ['id', 'short_name_e', 'short_name_b', 'thana', 'district', 'web_logo', 'format', 'google_map', 'defaultColor', 'payment_type', 'nidServicestatus', 'nidService'];

        $uniouninfos = Uniouninfo::where('short_name_e', $shortName)->select($columns)->first();

        if (!$uniouninfos) {
            return response()->json(['message' => 'No data found'], 404);
        }




        $sonod_name_lists = Sonodnamelist::with(['sonodFees' => function ($query) use ($shortName) {
            $query->where('unioun', $shortName);
        }])->select(['id', 'service_id', 'bnname', 'enname', 'icon'])->get();

        $returnData = [
            'uniouninfos'=>$uniouninfos,
            'sonod_name_lists'=>$sonod_name_lists,
        ];



        return response()->json($returnData, 200);
    }
}
