<?php

use Carbon\Carbon;
use App\Models\TokenBlacklist;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;



function TokenBlacklist($token){
// Get the authenticated user for each guard
    $user = null;
    $guardType = null;

    if (Auth::guard('admin')->check()) {
        $user = Auth::guard('admin')->user();
        $guardType = 'admin';
    } elseif (Auth::guard('user')->check()) {
        $user = Auth::guard('user')->user();
        $guardType = 'user';
    }


    TokenBlacklist::create([
            'token' => $token,
            'user_id' => $user->id,
            'user_type' => $guardType,
            'date' => Carbon::now(),
            ]);
}



function validateRequest(array $data, array $rules)
{
    $validator = Validator::make($data, $rules);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    return null; // Return null if validation passes
}


function int_en_to_bn($number)
{
    $bn_digits = array('০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯');
    $en_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

    return str_replace($en_digits, $bn_digits, $number);
}
function int_bn_to_en($number)
{

    $bn_digits = array('০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯');
    $en_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

    return str_replace($bn_digits, $en_digits, $number);
}



function getBanglaPositionText($position)
{
    $positionMap = [
        'District_admin' => 'জেলা প্রশাসকের ড্যাশবোর্ড',
        'DLG' => 'পরিচালক, (যুগ্মসচিব) স্থানীয় সরকার',
        'super_admin' => 'সুপার এডমিনের ড্যাশবোর্ড',
        'Sub_District_admin' => 'উপ-পরিচালকের ড্যাশবোর্ড',
        'Chairman' => 'চেয়ারম্যানের ড্যাশবোর্ড',
        'Secretary' => 'সচিবের ড্যাশবোর্ড',
    ];

    // Return the Bangla text for the position, or a default value if not found
    return $positionMap[$position] ?? 'উপজেলা ড্যাশবোর্ড';
}


 function getBanglaDesignationText($position)
{
    $designationMap = [
        'District_admin' => 'জেলা প্রশাসক',
        'DLG' => 'পরিচালক, (যুগ্মসচিব) স্থানীয় সরকার',
        'super_admin' => 'সুপার এডমিন',
        'Sub_District_admin' => 'উপ-পরিচালক',
        'Chairman' => 'চেয়ারম্যান',
        'Secretary' => 'সচিব',
    ];

    // Return the Bangla text for the designation, or a default value if not found
    return $designationMap[$position] ?? 'উপজেলা কর্মকর্তা';
}






