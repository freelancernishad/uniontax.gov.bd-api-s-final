<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    /**
     * Get bank accounts by the authenticated user's union.
     */
    public function getByUnion()
    {
        $union = Auth::user()->unioun; // Get the user's union

        $account = BankAccount::where('union', $union)->first();

        if (!$account) {
            // Return a default bank account structure
            return response()->json([
                "id" => null,
                "bank_name" => "",
                "branch_name" => "",
                "account_no" => "",
                "account_name" => "",
                "routing_no" => "",
                "union" => $union,
                "created_at" => now(),
                "updated_at" => now()
            ], 200);
        }

        return response()->json($account, 200);
    }

    /**
     * Update or create a bank account using the authenticated user's union.
     */
    public function updateOrCreateByUnion(Request $request)
    {
        $validatedData = $request->validate([
            'bank_name'   => 'required|string',
            'branch_name' => 'required|string',
            'account_no'  => 'required|string',
            'account_name'=> 'required|string',
            'routing_no'  => 'required|string',
        ]);

        $union = Auth::user()->unioun; // Get the user's union

        $bankAccount = BankAccount::updateOrCreate(
            ['union' => $union], // Search by the user's union
            array_merge($validatedData, ['union' => $union]) // Ensure the union is set
        );

        return response()->json([
            'message' => 'Bank account updated or created successfully',
            'data' => $bankAccount
        ], 200);
    }
}
