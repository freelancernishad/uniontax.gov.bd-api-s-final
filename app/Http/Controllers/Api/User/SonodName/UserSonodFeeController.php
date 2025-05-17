<?php

namespace App\Http\Controllers\Api\User\SonodName;

use App\Models\SonodFee;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use App\Models\Sonodnamelist;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class UserSonodFeeController extends Controller
{

    // Create multiple SonodFees
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fees_data' => 'required|array',
            'fees_data.*.sonodnamelist_id' => 'required|exists:sonodnamelists,id',
            'fees_data.*.service_id' => 'required',
            'fees_data.*.fees' => 'required|numeric',
            'fees_data.*.unioun' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        $sonodFees = [];

        foreach ($request->fees_data as $feeData) {
            $sonodFees[] = [
                'sonodnamelist_id' => $feeData['sonodnamelist_id'],
                'service_id' => $feeData['service_id'],
                'fees' => $feeData['fees'],
                'unioun' => $feeData['unioun'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert all fees in a single query
        SonodFee::insert($sonodFees);

        return response()->json([
            'status' => 'success',
            'message' => 'SonodFees created successfully'
        ], 201);
    }

    // Update multiple SonodFees
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fees_data' => 'required|array',
            'fees_data.*.sonod_fees_id' => 'nullable|exists:sonod_fees,id', // Make this nullable for creation
            'fees_data.*.sonodnamelist_id' => 'required|exists:sonodnamelists,id',
            'fees_data.*.service_id' => 'required',
            'fees_data.*.fees' => 'required|numeric',
            'fees_data.*.unioun' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        foreach ($request->fees_data as $feeData) {
            // Use updateOrCreate to either update the existing record or create a new one
            SonodFee::updateOrCreate(
                [
                    'id' => $feeData['sonod_fees_id'] ?? null, // Use the ID if provided, otherwise null
                ],
                [
                    'sonodnamelist_id' => $feeData['sonodnamelist_id'],
                    'service_id' => $feeData['service_id'],
                    'fees' => $feeData['fees'],
                    'unioun' => $feeData['unioun'],
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'SonodFees updated or created successfully'
        ], 200);
    }


     /**
     * Get Sonodnamelists with associated SonodFees data
     *
     * @return \Illuminate\Http\Response
     */
    public function getSonodnamelistsWithFees(Request $request)
    {
        // Check if the request is from admin with Bearer token or request token
        if (Auth::guard('admin')->check()) {
            // Admin authenticated via session (Bearer token already in place)
            $user = Auth::guard('admin')->user();
            $userUnioun = $request->union;
        } elseif ($request->has('token')) {
            // Admin authenticated via request token
            $user = Auth::guard('api')->setToken($request->token)->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized, invalid token'], 401); // Unauthorized if no valid token
            }
            $userUnioun = $request->union;
        } elseif (Auth::guard('user')->check()) {
            // User authenticated directly via session
            $user = Auth::guard('user')->user();
            $userUnioun = $user->unioun;
        } else {
            return response()->json(['error' => 'Unauthorized, valid token or session required'], 401); // Unauthorized if neither admin nor user authenticated
        }

        // Retrieve Union Information
        $uniouninfo = Uniouninfo::where('short_name_e', $userUnioun)->first();

        // Retrieve Sonodnamelists with fees for the user's union
        $sonodnamelists = Sonodnamelist::with(['sonodFees' => function ($query) use ($userUnioun) {
            $query->where('unioun', $userUnioun);
        }])->get();

        // Transform the data
        $data = $sonodnamelists->map(function ($sonodnamelist) use ($userUnioun) {
            $fee = $sonodnamelist->sonodFees->first();
            $fees = $fee ? $fee->fees : null;

            // Set fee to 200 if sonodnamelist_id is 2
            // if ($sonodnamelist->id == 2) {
            //     $fees = 200;
            // }

            return [
                'sonod_fees_id' => $fee->id ?? null,
                'sonodnamelist_id' => $sonodnamelist->id,
                'service_id' => $sonodnamelist->service_id,
                'bnname' => $sonodnamelist->bnname,
                'template' => $sonodnamelist->template,
                'unioun' => $userUnioun,
                'fees' => $fees,
            ];
        })->filter();

        // Check if the request wants a PDF
        if ($request->has('pdf')) {
            $html = View::make('pdf.SonodFees', ['data' => $data, 'uniouninfo' => $uniouninfo])->render();
            return generatePdf($html);
        }

        return response()->json([
            'data' => $data,
            'uniouninfo' => $uniouninfo
        ]);
    }







}
