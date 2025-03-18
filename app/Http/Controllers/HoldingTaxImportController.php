<?php

namespace App\Http\Controllers;

use App\Models\Holdingtax;
use Illuminate\Http\Request;
use App\Models\HoldingBokeya;
use App\Exports\HoldingTaxExport;
use App\Imports\HoldingTaxImport;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class HoldingTaxImportController extends Controller
{
    public function import(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Retrieve 'unioun' from the authenticated user
        $user = Auth::user();
        $unioun = $user->unioun;  // Assuming the 'unioun' field exists on the 'User' model

        // Initialize an array to hold imported data
        $importedData = [];

        // Import the Excel file
        Excel::import(new HoldingTaxImport($unioun, $importedData), $request->file('file'));

        // Return the imported data along with a success message
        return response()->json([
            'message' => 'Holding taxes imported successfully',
            'imported_data' => $importedData  // Include the imported records
        ], 200);
    }



    public function export(Request $request)
    {
        $token = $request->query('token');
        $word_no = $request->query('word_no');

        if (!$token) {
            return response()->json(['error' => 'No token provided.'], 400);
        }

        try {
            $authenticatedEntity = JWTAuth::setToken($token)->authenticate();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized. Invalid token.'], 403);
        }

        if (!$authenticatedEntity) {
            return response()->json(['error' => 'Unauthorized. Invalid token.'], 403);
        }

        // Get the 'unioun_name' from the authenticated entity
        $uniounName = $authenticatedEntity->unioun;

        // Filter HoldingTax records by unioun_name and select specific columns
        $holdingTaxRecords = Holdingtax::where(['unioun'=> $uniounName,'word_no' => $word_no])
                                    ->select(
                                            'id',
                                            'category',
                                            'holding_no',
                                            'maliker_name',
                                            'father_or_samir_name',
                                            'gramer_name',
                                            'word_no',
                                            'nid_no',
                                            'mobile_no',
                                            'griher_barsikh_mullo',
                                            'jomir_vara',
                                            'barsikh_vara',
                                            'image',
                                            'busnessName'
                                            )
                                    ->get();


    // Generate dynamic filename with Word No, Union Name, and Current Date-Time
    $dateTime = now()->format('Y-m-d_H-i-s');
    $fileName = "holding_tax_{$uniounName}_word_{$word_no}_{$dateTime}.xlsx";

    // Generate the Excel export with the filtered records
    return Excel::download(new HoldingTaxExport($holdingTaxRecords), $fileName);
    }



}
