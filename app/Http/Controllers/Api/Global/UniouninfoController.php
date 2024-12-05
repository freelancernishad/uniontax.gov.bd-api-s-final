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
        $shortName = $request->query('short_name_e');

        if (!$shortName) {
            return response()->json(['error' => 'short_name_e is required'], 400);
        }

        $uniouninfos = Uniouninfo::where('short_name_e', $shortName)->get();

        if ($uniouninfos->isEmpty()) {
            return response()->json(['message' => 'No data found'], 404);
        }

        return response()->json(['uniouninfos'=>$uniouninfos], 200);
    }
}
