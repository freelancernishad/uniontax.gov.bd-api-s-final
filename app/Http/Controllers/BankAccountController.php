<?php

namespace App\Http\Controllers;

use App\Models\Uniouninfo;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Devfaysal\BangladeshGeocode\Models\Upazila;

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


    public function getBankAccountsByUpazila(Request $request, $upazilaId)
    {
        // Upazila তথ্য নিয়ে আসা
        $upazila = Upazila::with(['unions', 'district'])->find($upazilaId);

        if (!$upazila) {
            return response()->json([
                'message' => 'Upazila not found',
            ], 404);
        }

        // ইউনিয়নের নামের ট্রান্সফরমেশন
        $unionNames = $upazila->unions->pluck('name')->map(function ($name) {
            return str_replace(' ', '', strtolower($name));
        })->toArray();

        // ইউনিয়ন ইনফো নিয়ে আসা
        $uniouninfos = Uniouninfo::whereIn('short_name_e', $unionNames)->get()->keyBy('short_name_e');

        // ব্যাংক অ্যাকাউন্ট তথ্য নিয়ে আসা
        $bankAccounts = BankAccount::whereIn('union', $unionNames)->get();

        // ফরম্যাট করা ব্যাংক তথ্য
        $formatted = $bankAccounts->map(function ($bankAccount) use ($uniouninfos) {
            $unionShortName = $bankAccount->union;
            $ekpayUserId = optional($uniouninfos[$unionShortName] ?? null)->AKPAY_MER_REG_ID ?? null;

            return [
                'union_name' => ucwords(str_replace('-', ' ', $unionShortName)),
                'bank_name' => $bankAccount->bank_name,
                'branch_name' => $bankAccount->branch_name,
                'account_name' => $bankAccount->account_name,
                'account_no' => $bankAccount->account_no,
                'routing_no' => $bankAccount->routing_no,
                'ekpay_user_id' => $ekpayUserId,
            ];
        });

        // Upazila এবং District এর নাম
        $upazilaName = $upazila->name;
        $upazilaName_bn = $upazila->bn_name;
        $districtName = $upazila->district->name ?? 'Unknown';
        $districtName_bn = $upazila->district->bn_name ?? 'Unknown';
        $unoName = ""; // UNO নাম যদি দরকার হয়

        // PDF ফাইলের নাম
        $filename = "banklist-$upazila->name.pdf";

        // UNO সাইন চেক করা হবে
        $generateWithUnoApproval = $request->boolean('unoSign');

        // UNO সাইন থাকলে এক্সপোর্ট হবে, না থাকলে সাধারন ব্যাংক লিস্ট
        if ($generateWithUnoApproval) {
            // UNO অনুমতি সহ PDF তৈরি
            $htmlView = view('BankDetails.bankListWithUnoApproval', compact('formatted', 'upazilaName', 'districtName','upazilaName_bn','districtName_bn', 'unoName'))->render();
        } else {
            // সাধারন ব্যাংক লিস্ট PDF তৈরি
            $htmlView = view('BankDetails.bankListByThana', compact('formatted', 'upazilaName', 'districtName','upazilaName_bn','districtName_bn',))->render();
        }

        // PDF তৈরি করা
        generatePdf($htmlView, null, null, $filename);

        // রেসপন্স দেওয়া
        return response()->json($formatted, 200);
    }







}
