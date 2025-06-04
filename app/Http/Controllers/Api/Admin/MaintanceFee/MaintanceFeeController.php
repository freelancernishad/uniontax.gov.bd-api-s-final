<?php

// app/Http/Controllers/Api/Admin/MaintanceFee/MaintanceFeeController.php

namespace App\Http\Controllers\Api\Admin\MaintanceFee;

use App\Models\Uniouninfo;
use App\Models\MaintanceFee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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



public function unionListByStatus(Request $request)
{
    $validated = $request->validate([
        'type' => 'required|in:monthly,yearly',
        'period' => ['required', 'string', function ($attribute, $value, $fail) use ($request) {
            if ($request->type === 'monthly' && !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)) {
                $fail('For monthly type, period must be in YYYY-MM format.');
            }
            if ($request->type === 'yearly' && !preg_match('/^\d{4}-\d{2}$/', $value)) {
                $fail('For yearly type, period must be in format like 2023-24.');
            }
        }],
        'status' => 'required|in:paid,pending',
        'division_name' => 'nullable|string',
        'district_name' => 'nullable|string',
        'upazila_name' => 'nullable|string',
    ]);

    $type = $validated['type'];
    $period = $validated['period'];
    $status = $validated['status'];

    // Get all MaintenanceFee entries matching type + period + paid
    $matchedFees = MaintanceFee::where([
        'type' => $type,
        'period' => $period,
        'status' => 'paid'
    ])->get();

    $paidUnionNames = $matchedFees->pluck('union')->toArray();

    // Apply union filtering based on status
    $query = Uniouninfo::query();

    if ($status === 'paid') {
        $query->whereIn('short_name_e', $paidUnionNames);
    } else {
        $query->whereNotIn('short_name_e', $paidUnionNames);
    }

    // Optional location filters
    if (!empty($validated['division_name'])) {
        $query->where('division_name', $validated['division_name']);
    }
    if (!empty($validated['district_name'])) {
        $query->where('district_name', $validated['district_name']);
    }
    if (!empty($validated['upazila_name'])) {
        $query->where('upazila_name', $validated['upazila_name']);
    }

    $unions = $query->select(
        'full_name',
        'short_name_e',
        'division_name',
        'district_name',
        'upazila_name',
        'chairman_phone',
        'secretary_phone',
        'udc_phone',
        'user_phone',
        'maintance_fee',
        'maintance_fee_type',
        'maintance_fee_option'
    )->get();

    // Always attach payment_info — either real or null
    $unions = $unions->map(function ($union) use ($matchedFees) {
        $paid = $matchedFees->where('union', $union->short_name_e)->first();

        return [
            'full_name' => $union->full_name,
            'short_name_e' => $union->short_name_e,
            'division_name' => $union->division_name,
            'district_name' => $union->district_name,
            'upazila_name' => $union->upazila_name,
            'chairman_phone' => $union->chairman_phone,
            'secretary_phone' => $union->secretary_phone,
            'udc_phone' => $union->udc_phone,
            'user_phone' => $union->user_phone,
            'maintance_fee' => $union->maintance_fee,
            'maintance_fee_type' => $union->maintance_fee_type,
            'maintance_fee_option' => $union->maintance_fee_option,
            'payment_info' => [
                'maintance_fee_id' => $paid->id ?? null,
                'amount' => $paid->amount ?? null,
                'transaction_fee' => $paid->transaction_fee ?? null,
                'type' => $paid->type ?? null,
                'period' => $paid->period ?? null,
                'paid_at' => $paid->payment_date ?? null,
            ],
        ];
    });

    return response()->json( $unions->values());
}










}
