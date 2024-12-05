<?php

namespace App\Http\Controllers\Api\Global;

use App\Http\Controllers\Controller;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;

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
        $shortName = $request->query('name');

        if (!$shortName) {
            return response()->json(['error' => 'short_name_e is required'], 400);
        }

        $columns = ['id', 'short_name_e', 'short_name_b', 'thana', 'district', 'web_logo', 'format', 'google_map', 'defaultColor', 'payment_type', 'nidServicestatus', 'nidService'];

        $uniouninfos = Uniouninfo::where('short_name_e', $shortName)->select($columns)->first();

        if ($uniouninfos->isEmpty()) {
            return response()->json(['message' => 'No data found'], 404);
        }

        return response()->json(['uniouninfos'=>$uniouninfos], 200);
    }
}
