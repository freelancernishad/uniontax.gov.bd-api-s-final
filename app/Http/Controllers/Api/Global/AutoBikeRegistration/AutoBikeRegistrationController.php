<?php

namespace App\Http\Controllers\Api\Global\AutoBikeRegistration;

use App\Models\Payment;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AutoBikeRegistration;
use Illuminate\Support\Facades\Validator;

class AutoBikeRegistrationController extends Controller
{
    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'fiscal_year' => 'required|string|max:255',
            'application_type' => 'required|string|max:255',
            'applicant_name_bn' => 'required|string|max:255',
            'applicant_name_en' => 'required|string|max:255',
            'applicant_father_name' => 'required|string|max:255',
            'applicant_mother_name' => 'required|string|max:255',
            'applicant_gender' => 'required|string|max:10',
            'nationality' => 'required|string|max:100',
            'applicant_religion' => 'required|string|max:100',
            'applicant_date_of_birth' => 'required|date',
            'marital_status' => 'required|string|max:50',
            'profession' => 'required|string|max:255',
            'blood_group' => 'required|string|max:5',
            'applicant_mobile' => 'required|string|max:20',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relation' => 'required|string|max:100',
            'emergency_contact_national_id_number' => 'required|string|max:30',
            'auto_bike_purchase_date' => 'required|date',
            'auto_bike_last_renew_date' => 'nullable|date',
            'auto_bike_supplier_name' => 'required|string|max:255',
            'auto_bike_supplier_address' => 'required|string|max:500',
            'auto_bike_supplier_mobile' => 'required|string|max:20',


            // Newly added location-related fields
            'current_division' => 'nullable|string|max:255',
            'applicant_present_district' => 'nullable|string|max:255',
            'applicant_present_Upazila' => 'nullable|string|max:255',
            'applicant_present_union' => 'nullable|string|max:255',
            'permanent_address' => 'nullable|string|max:500',
            'applicant_permanent_division' => 'nullable|string|max:255',
            'applicant_permanent_district' => 'nullable|string|max:255',
            'applicant_permanent_Upazila' => 'nullable|string|max:255',
            'applicant_permanent_union' => 'nullable|string|max:255',



            'passport_photo' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'national_id_copy' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'auto_bike_receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'previous_license_copy' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'affidavit_copy' => 'nullable|file|mimes:jpg,jpeg,png,pdf',

            'union_name' => 'required|string|max:255', // NEW
            'c_uri' => 'required|string',
            'f_uri' => 'required|string',
            's_uri' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }


        $data = $validator->validated();

        // ফাইল আপলোড এবং path আপডেট
        $fileFields = ['passport_photo', 'national_id_copy', 'auto_bike_receipt', 'previous_license_copy', 'affidavit_copy'];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                // তোমার uploadDocumentsToS3() হেল্পার ইউজ করে আপলোড করো
                $url = uploadDocumentsToS3($file, 'auto_bike_registration', now()->format('Y-m-d'), null);
                if ($url) {
                    $data[$field] = $url;
                }
            }
        }

        $registration = AutoBikeRegistration::create($data);

        $urls = [
            'c_uri' => $request->input('c_uri'),
            'f_uri' => $request->input('f_uri'),
            's_uri' => $request->input('s_uri'),
        ];

        $query = http_build_query($urls);
        $redirectUrl = route('auto_bike_registration.payment', ['id' => $registration->id]) . '?' . $query;

        return response()->json([
            'status' => true,
            'message' => 'Auto bike registration created successfully',
            // 'data' => $registration,
            'redirect_url' => $redirectUrl,
        ], 201);
    }


    public function payment(Request $request, $id)
    {
        // AutoBikeRegistration রেকর্ড পাওয়া
        $autoBike = AutoBikeRegistration::find($id);
        if (!$autoBike) {
            return response()->json([
                'status' => false,
                'message' => 'Registration record not found.'
            ], 404);
        }

        // ইউনিয়ন এর তথ্য নাও
        $unioun_name = $autoBike->union_name;
        $uniouninfo = Uniouninfo::where('short_name_e', $unioun_name)->first();

        if (!$uniouninfo) {
            return response()->json([
                'status' => false,
                'message' => 'Union information not found.'
            ], 404);
        }

        $applicant_mobile = $autoBike->applicant_mobile;
        $total_amount = 200; // তোমার পেমেন্ট এমাউন্ট (ডায়নামিক করতে পারো)

        $trnx_id = $uniouninfo->u_code . '-' . time();

        $cust_info = [
            "cust_email" => "",
            "cust_id" => (string) $autoBike->id,
            "cust_mail_addr" => "Address",
            "cust_mobo_no" => $applicant_mobile,
            "cust_name" => $autoBike->applicant_name_en
        ];

        $trns_info = [
            "ord_det" => 'auto_bike',
            "ord_id" => (string) $autoBike->id,
            "trnx_amt" => $total_amount,
            "trnx_currency" => "BDT",
            "trnx_id" => $trnx_id
        ];

        $urls = [
            'c_uri' => $request->input('c_uri'),
            'f_uri' => $request->input('f_uri'),
            's_uri' => $request->input('s_uri'),
        ];

        $redirectUrl = ekpayToken($trnx_id, $trns_info, $cust_info, 'payment', $unioun_name, $urls);

        $req_timestamp = now();

        Payment::create([
            'union' => $unioun_name,
            'trxId' => $trnx_id,
            'transaction_id' => $trnx_id,
            'gateway' => 'ekpay',
            'amount' => $total_amount,
            'sonodId' => $autoBike->id,
            'sonod_type' => 'AutoBikeRegistration',
            'applicant_mobile' => $applicant_mobile,
            'status' => 'Pending',
            'paymentUrl' => !is_array($redirectUrl) ? $redirectUrl : '',
            'ipnResponse' => is_array($redirectUrl) ? json_encode($redirectUrl) : '',
            'method' => 'ekpay',
            'payment_type' => 'online',
            'date' => $req_timestamp->format('Y-m-d'),
            'hasEnData' => 0,
            'uddoktaId' => null,
        ]);

        return redirect($redirectUrl);
    }

}
