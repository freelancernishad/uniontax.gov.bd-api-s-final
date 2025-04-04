<?php

namespace App\Http\Controllers\Api\User\Uniouninfo;

use App\Models\Village;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Devfaysal\BangladeshGeocode\Models\Union;

class VillageController extends Controller
{
    /**
     * Get all Villages by Union short_name_e (Public).
     */
    public function index(Request $request)
    {
        $short_name_e = Auth::check() ? Auth::user()->unioun : $request->short_name_e;

        $union = Uniouninfo::where('short_name_e', $short_name_e)->firstOrFail();

        $villagesQuery = $union->villages();

        if ($request->has('word_no')) {
            $villagesQuery->where('word_no', $request->word_no);
        }

        return response()->json($villagesQuery->get());
    }


    function getVillageByUnionWord($union,$word) {

        $unions = Union::where('id', $union)->pluck('name')->map(function ($name) {
            return strtolower(str_replace(' ', '', $name));
        });
        $unioninfo_id = Uniouninfo::where('short_name_e', $unions)->firstOrFail()->id;
        $word_no = $word;
        $village = Village::where('unioninfo_id', $unioninfo_id)->where('word_no', $word_no)->get();
        return response()->json($village);

    }




    /**
     * Store a new Village (Authenticated).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_bn' => 'required|string',
            'name_en' => 'nullable|string',
            'word_no' => 'required|integer',
        ]);

        $short_name_e = Auth::user()->unioun;

        $union = Uniouninfo::where('short_name_e', $short_name_e)->firstOrFail();

        $village = Village::create([
            'unioninfo_id' => $union->id,
            'name_bn' => $request->name_bn,
            'name_en' => $request->name_en,
            'word_no' => $request->word_no,
        ]);

        return response()->json(['message' => 'Village created successfully', 'data' => $village], 201);
    }

    /**
     * Update a Village (Authenticated).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_bn' => 'required|string',
            'name_en' => 'nullable|string',
            'word_no' => 'required|integer',
        ]);

        $village = Village::findOrFail($id);
        $village->update($request->all());

        return response()->json(['message' => 'Village updated successfully', 'data' => $village]);
    }

    /**
     * Delete a Village (Authenticated).
     */
    public function destroy($id)
    {
        $village = Village::findOrFail($id);
        $village->delete();

        return response()->json(['message' => 'Village deleted successfully']);
    }
}
