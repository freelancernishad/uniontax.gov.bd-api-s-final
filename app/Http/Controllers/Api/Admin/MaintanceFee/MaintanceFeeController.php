<?php

// app/Http/Controllers/Api/Admin/MaintanceFee/MaintanceFeeController.php

namespace App\Http\Controllers\Api\Admin\MaintanceFee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintanceFee;

class MaintanceFeeController extends Controller
{
    public function index()
    {
        $fees = MaintanceFee::all();
        return response()->json($fees);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'union' => 'required|string',
            'amount' => 'required|numeric',
            'status' => 'required|string',
            'payment_date' => 'required|date',
            'type' => 'required|in:monthly,yearly',
        ]);

        $fee = MaintanceFee::create($validated);
        return response()->json($fee, 201);
    }

    public function show($id)
    {
        $fee = MaintanceFee::findOrFail($id);
        return response()->json($fee);
    }

    public function update(Request $request, $id)
    {
        $fee = MaintanceFee::findOrFail($id);

        $validated = $request->validate([
            'union' => 'sometimes|string',
            'amount' => 'sometimes|numeric',
            'status' => 'sometimes|string',
            'payment_date' => 'sometimes|date',
            'type' => 'sometimes|in:monthly,yearly',
        ]);

        $fee->update($validated);
        return response()->json($fee);
    }

    public function destroy($id)
    {
        $fee = MaintanceFee::findOrFail($id);
        $fee->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
