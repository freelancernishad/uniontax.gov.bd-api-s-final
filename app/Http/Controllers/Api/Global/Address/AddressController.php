<?php

namespace App\Http\Controllers\Api\Global\Address;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Devfaysal\BangladeshGeocode\Models\Division;
use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use Devfaysal\BangladeshGeocode\Models\Union;

class AddressController extends Controller
{
    /**
     * Get all divisions
     */
    public function getDivisions()
    {
        $divisions = Division::all();
        return response()->json($divisions);
    }

    /**
     * Get districts by division ID
     */
    public function getDistrictsByDivision($division_id)
    {
        $division = Division::find($division_id);

        if (!$division) {
            return response()->json([
                'success' => false,
                'message' => 'Division not found'
            ], 404);
        }

        $districts = $division->districts;
        return response()->json($districts);
    }

    /**
     * Get upazilas by district ID
     */
    public function getUpazilasByDistrict($district_id)
    {
        $district = District::find($district_id);

        if (!$district) {
            return response()->json([
                'success' => false,
                'message' => 'District not found'
            ], 404);
        }

        $upazilas = $district->upazilas;
        return response()->json($upazilas);
    }

    /**
     * Get unions by upazila ID
     */
    public function getUnionsByUpazila($upazila_id)
    {
        $upazila = Upazila::find($upazila_id);

        if (!$upazila) {
            return response()->json([
                'success' => false,
                'message' => 'Upazila not found'
            ], 404);
        }

        $unions = $upazila->unions; // using relationship if defined in Upazila model

        return response()->json($unions);
    }
}
