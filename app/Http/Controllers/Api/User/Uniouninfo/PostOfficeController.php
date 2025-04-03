<?php

namespace App\Http\Controllers\Api\User\Uniouninfo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostOffice;
use App\Models\Uniouninfo;
use Devfaysal\BangladeshGeocode\Models\Union;
use Illuminate\Support\Facades\Auth;

class PostOfficeController extends Controller
{
    /**
     * Get all Post Offices by Union short_name_e (Public).
     */
    public function index(Request $request)
    {
        $short_name_e = Auth::check() ? Auth::user()->unioun : $request->short_name_e;

        $union = Uniouninfo::where('short_name_e', $short_name_e)->firstOrFail();
        return response()->json($union->postOffices);
    }
    function getPostOfficeByUnion($upazila_id) {


$unions = Union::where('upazila_id', $upazila_id)->get();

        // $unioninfo_id = Uniouninfo::where('short_name_e', $union)->firstOrFail()->id;
        // $post_office = PostOffice::where('unioninfo_id', $unioninfo_id)->get();
        return response()->json($unions);

    }

    /**
     * Store a new Post Office (Authenticated).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_bn' => 'required|string',
            'name_en' => 'nullable|string',
            'post_code' => 'required|string',
        ]);
        $short_name_e = Auth::user()->unioun;

        $union = Uniouninfo::where('short_name_e', $short_name_e)->firstOrFail();

        $postOffice = PostOffice::create([
            'unioninfo_id' => $union->id,
            'name_bn' => $request->name_bn,
            'name_en' => $request->name_en,
            'post_code' => $request->post_code,
        ]);

        return response()->json(['message' => 'Post Office created successfully', 'data' => $postOffice], 201);
    }

    /**
     * Update a Post Office (Authenticated).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_bn' => 'required|string',
            'name_en' => 'nullable|string',
            'post_code' => 'required|string',
        ]);

        $postOffice = PostOffice::findOrFail($id);
        $postOffice->update($request->all());

        return response()->json(['message' => 'Post Office updated successfully', 'data' => $postOffice]);
    }

    /**
     * Delete a Post Office (Authenticated).
     */
    public function destroy($id)
    {
        $postOffice = PostOffice::findOrFail($id);
        $postOffice->delete();

        return response()->json(['message' => 'Post Office deleted successfully']);
    }
}
