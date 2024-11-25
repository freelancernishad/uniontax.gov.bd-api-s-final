<?php

namespace App\Http\Controllers\Api\Admin\SonodName;

use App\Http\Controllers\Controller;
use App\Models\Sonodnamelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminSonodnamelistController extends Controller
{
    /**
     * Display a listing of the Sonodnamelists.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sonodnamelists = Sonodnamelist::all();  // You can also add pagination if needed
        return response()->json($sonodnamelists);
    }

    /**
     * Store a newly created Sonodnamelist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|integer',
            'bnname' => 'required|string',
            'enname' => 'required|string',
            'icon' => 'nullable|string',
            'template' => 'nullable|string',
            'sonod_fee' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 422);
        }

        // Create new Sonodnamelist
        $sonodnamelist = Sonodnamelist::create($request->all());
        if ($request->hasFile('icon')) {
            $sonodnamelist->saveIcon($request->file('icon'));
        }

        return response()->json($sonodnamelist);
    }

    /**
     * Display the specified Sonodnamelist.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sonodnamelist = Sonodnamelist::find($id);

        if (!$sonodnamelist) {
            return response()->json([
                'message' => 'Sonodnamelist not found'
            ], 404);
        }

        return response()->json($sonodnamelist);
    }

    /**
     * Update the specified Sonodnamelist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|integer',
            'bnname' => 'required|string',
            'enname' => 'required|string',
            'icon' => 'nullable|string',
            'template' => 'nullable|string',
            'sonod_fee' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }

        // Find Sonodnamelist by ID
        $sonodnamelist = Sonodnamelist::find($id);

        if (!$sonodnamelist) {
            return response()->json([
                'message' => 'Sonodnamelist not found'
            ], 404);
        }

        // Update Sonodnamelist
        $sonodnamelist->update($request->all());
        if ($request->hasFile('icon')) {
            $sonodnamelist->saveIcon($request->file('icon'));
        }

        return response()->json($sonodnamelist);
    }

    /**
     * Remove the specified Sonodnamelist.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sonodnamelist = Sonodnamelist::find($id);

        if (!$sonodnamelist) {
            return response()->json([
                'message' => 'Sonodnamelist not found'
            ], 404);
        }

        // Delete Sonodnamelist
        $sonodnamelist->delete();

        return response()->json([
            'message' => 'Sonodnamelist deleted successfully'
        ]);
    }
}
