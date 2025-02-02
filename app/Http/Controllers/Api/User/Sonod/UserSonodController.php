<?php

namespace App\Http\Controllers\Api\User\Sonod;

use App\Models\User;
use App\Models\Sonod;
use App\Models\Uniouninfo;
use App\Models\EnglishSonod;
use Illuminate\Http\Request;
use App\Models\Sonodnamelist;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserSonodController extends Controller
{
    /**
     * Display a listing of Sonods for authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Retrieve request parameters
        $sonod_name = $request->sonod_name;
        $stutus = $request->stutus;
        $payment_status = $request->payment_status;
        $sondId = $request->sondId;

        // Initialize the query
        $query = Sonod::query()->where('sonod_name', $sonod_name);

        // Select the required fields including the english_sonod relationship
        $query->select(
            'id',
            'sonod_name',
            'unioun_name',
            'applicant_name',
            'applicant_father_name',
            'applicant_present_word_number',
            'created_at',
            'stutus',
            'payment_status',
            'sonod_Id',
            'prottoyon',
            'hasEnData',
            'created_at',
            'updated_at'
        );

        // Filter by union name if provided
        if (Auth::guard('user')->check()) {
            // Retrieve the authenticated user from the Bearer token
            $user = Auth::user();

            // Check if the user is authenticated
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $union = $user->unioun;
            $position = $user->position;
        } else {
            $union = $request->union;
            $position = '';
        }

        $query->where('unioun_name', $union);

        // If the user is a Secretary and stutus is "Pending", filter the results
        if ($position == 'Secretary' && $stutus === 'Pending') {
            $query->where('stutus', 'Pending');
        } elseif ($position == 'Chairman' && $stutus === 'Pending') {
            $query->where('stutus', 'sec_approved');
        } else {
            $query->where('stutus', $stutus);
        }

        // Filter by payment status if provided
        if ($payment_status) {
            $query->where('payment_status', $payment_status);
        }

        // Filter by sonod Id if provided
        if ($sondId) {
            $query->where("sonod_Id", "LIKE", "%$sondId%");
        }

        // Eager load the english_sonod relationship
        $query->with(['english_sonod' => function ($query) {
            $query->select('id', 'sonod_Id','prottoyon'); // Select only the id and sonod_Id (foreign key)
        }]);

        // Paginate the results
        $sonods = $query->orderBy('id', 'DESC')->paginate(20);

        // Modify the response to include english_sonod id
        $sonods->getCollection()->transform(function ($sonod) {
            $sonod->english_sonod_id = $sonod->english_sonod ? $sonod->english_sonod->id : null;
            $sonod->english_prottoyon = $sonod->english_sonod ? $sonod->english_sonod->prottoyon : null;
            return $sonod;
        });

        // Return the data
        return response()->json([
            'sonods' => $sonods,
        ]);
    }


    public function sonod_action(Request $request, $id)
    {
        $sonod = Sonod::with('english_sonod')->find($id);

        // Return an error if the Sonod is not found
        if (!$sonod) {
            return response()->json(['message' => 'Sonod not found'], 404);
        }




        $user = Auth::user(); // Get the currently authenticated user

        $action = $request->action; // 'approve' or 'cancel'

        // Check if the action is cancellation
        if ($action === 'cancel') {
            return $this->cancelSonod($sonod, $user, $request->cancel_reason);
        }



        $sec_prottoyon_en = '';
        $sec_prottoyon = '';
        if($sonod->sonod_name=='বিবিধ প্রত্যয়নপত্র' || $sonod->sonod_name=='অনাপত্তি সনদপত্র'){
            $sec_prottoyon_en = $request->sec_prottoyon_en;
            $sec_prottoyon = $request->sec_prottoyon;
        }else{
            if($sonod->english_sonod){
                $sec_prottoyon_en = generateSecProttoyon($sonod->english_sonod,true);
            }
                $sec_prottoyon = generateSecProttoyon($sonod);
        }






        // Check if the user can perform an action based on their position and sonod status
        if ($user->position == 'Secretary' && $sonod->stutus == 'Pending') {
            // Secretary can approve, so set the status to 'sec_approved'
            $approveData = 'sec_approved';
        } elseif ($user->position == 'Secretary' && $sonod->stutus == 'cancel') {
            // Secretary can approve, so set the status to 'sec_approved'
            $approveData = 'sec_approved';
        } elseif ($user->position == 'Chairman' && $sonod->stutus == 'sec_approved') {
            // Chairman can approve, so set the status to 'approved'
            $approveData = 'approved';
        } else {
            // If neither condition is met, return an error response
            return response()->json(['message' => 'Action not allowed on this Sonod status'], 403);
        }

        // Retrieve union information and check for Postpaid payment type
        $unioninfo = Uniouninfo::where('short_name_e', $sonod->unioun_name)->latest()->first();
        $isPostpaid = $unioninfo && $unioninfo->payment_type == 'Postpaid';


        // Initialize the update data with common fields


        if($user->position=='Secretary'){

            $updateData = [
                'sec_prottoyon' => $sec_prottoyon,
                'stutus' => $approveData, // sec_approved or approved
            ];

            $updateData_en = [
                'sec_prottoyon' => $sec_prottoyon_en,
            ];
        }else{
            $updateData = [
                'stutus' => $approveData,
            ];
            $updateData_en = [];
        }

        $format = 1;
        if($sonod->sonod_name=='ওয়ারিশান সনদ' || $sonod->sonod_name=='উত্তরাধিকারী সনদ'){
            $format = 2;
        }
        if($sonod->sonod_name=='ট্রেড লাইসেন্স'){
            $format = 2;
        }




        // If Secretary is updating the Sonod, include socib_name, socib_signture, and socib_email
        if ($user->position == 'Secretary' && $unioninfo) {

            $updateData = array_merge($updateData, [
                'socib_name' => $unioninfo->socib_name ?? 'N/A',
                'socib_signture' => $unioninfo->socib_signture ?? 'N/A',
                'socib_email' => $unioninfo->socib_email ?? 'N/A',
                'format' => $format ?? 1,
            ]);

            $updateData_en = array_merge($updateData_en, [
                'socib_name' => $unioninfo->socib_name_en ?? 'N/A',
                'socib_signture' => $unioninfo->socib_signture ?? 'N/A',
                'socib_email' => $unioninfo->socib_email ?? 'N/A',
                'format' => $format ?? 1,
            ]);


        }



        // If the user is Chairman, add Chairman-specific information
        if ($user->position == 'Chairman' && $unioninfo) {

            $updateData = array_merge($updateData, [
                'chaireman_name' => $unioninfo->c_name ?? 'N/A',
                'chaireman_type' => $unioninfo->c_type ?? 'N/A',
                'c_email' => $unioninfo->c_email ?? 'N/A',
                'chaireman_sign' => $unioninfo->c_signture ?? 'N/A',
            ]);

            $updateData_en = array_merge($updateData_en, [
                'chaireman_name' => $unioninfo->c_name_en ?? 'N/A',
                'chaireman_type' => $unioninfo->c_type_en ?? 'N/A',
                'c_email' => $unioninfo->c_email ?? 'N/A',
                'chaireman_sign' => $unioninfo->c_signture ?? 'N/A',
            ]);

        }

        // If the user is Secretary and payment type is Postpaid, include additional fields
        if ($user->position == 'Secretary' && $isPostpaid) {
            $arraydata = [
                'total_amount' => $request->amounta,
                'pesaKor' => $request->pesaKor,
                'tredeLisenceFee' => $request->tredeLisenceFee,
                'vatAykor' => $request->vatAykor,
                'khat' => $request->khat,
                'last_years_money' => $request->last_years_money,
                'currently_paid_money' => $request->currently_paid_money,
            ];

            // Prepare money in words and encode the array data
            $the_amount_of_money_in_words = convertAnnualIncomeToText($request->amounta);
            $amount_deails = json_encode($arraydata);

            // Merge postpaid specific fields into the update data
            $updateData = array_merge($updateData, [
                'khat' => $request->khat,
                'last_years_money' => $request->last_years_money,
                'currently_paid_money' => $request->currently_paid_money,
                'total_amount' => $request->amounta,
                'the_amount_of_money_in_words' => $the_amount_of_money_in_words,
                'amount_deails' => $amount_deails,
            ]);
        }

        // Perform the update based on the user position


        $sonod->update($updateData);

        if($sonod->hasEnData){
            $enSonod = EnglishSonod::where('sonod_Id',$sonod->id)->first();
            $enSonod->update($updateData_en);
        }


        // If Secretary updated the Sonod, send notification to the Chairman
        if ($user->position == 'Secretary') {
            // $this->sendNotificationToChairman($sonod);
            return response()->json(['message' => 'Sonod status updated to sec_approved'], 200);
        }

        // If Chairman updated the Sonod, return a success message
        return response()->json(['message' => 'Sonod status updated to approved'], 200);
    }


    private function cancelSonod($sonod, $user, $cancel_reason='')
    {
        // Check if the user has the right to cancel the Sonod
        if (!in_array($user->position, ['Secretary', 'Chairman'])) {
            return response()->json(['message' => 'Cancellation not allowed'], 403);
        }

        // Update the Sonod status and record cancellation details
        $sonod->update([
            'stutus' => 'cancel',
            'cancel_reason' => $cancel_reason ?? 'No reason provided',
            'cancedby' => $user->position,
            'cancedbyUserid' => $user->id,
        ]);

        return response()->json([
            'message' => 'Sonod has been cancelled',
            'canceled_by' => $user->position,
            'canceled_by_user_id' => $user->id,
            'cancel_reason' => $cancel_reason
        ], 200);
    }




    public function update(Request $request, $id)
    {
        try {
            // Find the existing record
            $sonod = Sonod::with('english_sonod')->findOrFail($id);

            Log::info($request->all());
            // Filter the request data to only include fields that exist in the Sonod model
            $updatableFields = [
                'successor_father_name', 'successor_mother_name',
                'ut_father_name', 'ut_mother_name', 'ut_grame', 'ut_post', 'ut_thana',
                'ut_district', 'ut_word', 'successor_father_alive_status', 'successor_mother_alive_status',
                'applicant_holding_tax_number', 'applicant_national_id_number', 'applicant_birth_certificate_number',
                'applicant_passport_number', 'applicant_date_of_birth', 'family_name', 'Annual_income',
                'Annual_income_text', 'Subject_to_permission', 'disabled', 'The_subject_of_the_certificate',
                'Name_of_the_transferred_area', 'applicant_second_name', 'applicant_owner_type',
                'applicant_name_of_the_organization', 'organization_address', 'applicant_name', 'utname',
                'ut_religion', 'alive_status', 'applicant_gender', 'applicant_marriage_status',
                'applicant_vat_id_number', 'applicant_tax_id_number', 'applicant_type_of_business',
                'applicant_father_name', 'applicant_mother_name', 'applicant_occupation', 'applicant_education',
                'applicant_religion', 'applicant_resident_status', 'applicant_present_village',
                'applicant_present_road_block_sector', 'applicant_present_word_number', 'applicant_present_district',
                'applicant_present_Upazila', 'applicant_present_post_office', 'applicant_permanent_village',
                'applicant_permanent_road_block_sector', 'applicant_permanent_word_number',
                'applicant_permanent_district', 'applicant_permanent_Upazila', 'applicant_permanent_post_office',
                'successor_list', 'applicant_mobile', 'applicant_email', 'applicant_phone', 'prottoyon',
                'format', 'applicant_type_of_businessKhat', 'applicant_type_of_businessKhatAmount',
                'khat'
            ];

            // Extract only the fields that exist in the model from the request
            $dataToUpdate = $request->only($updatableFields);


            $sonodEnName = Sonodnamelist::where('bnname', $sonod->sonod_name)->first();


            $filePath = str_replace(' ', '_', $sonodEnName->enname);
            $dateFolder = date("Y/m/d");

            handleFileUploads($request, $dataToUpdate, $filePath, $dateFolder, $sonod->sonod_Id);

            // Log::info($dataToUpdate);
            // Update the Sonod record (excluding sec_prottoyon for now)
            $sonod->update($dataToUpdate);



            if ($request->has('sec_prottoyon')) {
                $sonod->update(['sec_prottoyon' => $request->sec_prottoyon]);
            }else{
                // Handle sec_prottoyon update after the main update is completed
                if ($sonod->sonod_name == 'বিবিধ প্রত্যয়নপত্র' || $sonod->sonod_name == 'অনাপত্তি সনদপত্র') {
                    // Update sec_prottoyon if provided in the request
                    if ($request->has('sec_prottoyon')) {
                        $sonod->update(['sec_prottoyon' => $request->sec_prottoyon]);
                    }
                } else {
                    // Generate and update sec_prottoyon for other sonod_name values
                    $sonod->update(['sec_prottoyon' => generateSecProttoyon($sonod)]);
                }
            }






            // Return the updated record in the response
            return response()->json([
                'message' => 'Sonod updated successfully',
                'sonod' => $sonod
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Sonod not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Update failed', 'error' => $e->getMessage()], 500);
        }
    }


    public function updateEnglishSonod(Request $request, $id)
    {
        try {
            // Find the Sonod record with its associated EnglishSonod
            $sonod = Sonod::with(['english_sonod' => function ($query) {
                $query->select('id', 'sonod_Id'); // Select only the id and sonod_Id (foreign key)
            }])->find($id);

            // Check if the Sonod record exists
            if (!$sonod) {
                return response()->json(['message' => 'Sonod not found'], 404);
            }

            // Check if the EnglishSonod exists
            if (!$sonod->english_sonod) {
                return response()->json(['message' => 'EnglishSonod not found for the given Sonod'], 404);
            }

            // Find the existing EnglishSonod record
            $englishSonod = EnglishSonod::findOrFail($sonod->english_sonod->id);

            // Filter the request data to only include fields that exist in the EnglishSonod model
            $updatableFields = [
                'successor_father_name', 'successor_mother_name',
                'ut_father_name', 'ut_mother_name', 'ut_grame', 'ut_post', 'ut_thana',
                'ut_district', 'ut_word', 'successor_father_alive_status', 'successor_mother_alive_status',
                'applicant_holding_tax_number', 'applicant_national_id_number', 'applicant_birth_certificate_number',
                'applicant_passport_number', 'applicant_date_of_birth', 'family_name', 'Annual_income',
                'Annual_income_text', 'Subject_to_permission', 'disabled', 'The_subject_of_the_certificate',
                'Name_of_the_transferred_area', 'applicant_second_name', 'applicant_owner_type',
                'applicant_name_of_the_organization', 'organization_address', 'applicant_name', 'utname',
                'ut_religion', 'alive_status', 'applicant_gender', 'applicant_marriage_status',
                'applicant_vat_id_number', 'applicant_tax_id_number', 'applicant_type_of_business',
                'applicant_father_name', 'applicant_mother_name', 'applicant_occupation', 'applicant_education',
                'applicant_religion', 'applicant_resident_status', 'applicant_present_village',
                'applicant_present_road_block_sector', 'applicant_present_word_number', 'applicant_present_district',
                'applicant_present_Upazila', 'applicant_present_post_office', 'applicant_permanent_village',
                'applicant_permanent_road_block_sector', 'applicant_permanent_word_number',
                'applicant_permanent_district', 'applicant_permanent_Upazila', 'applicant_permanent_post_office',
                'successor_list', 'applicant_mobile', 'applicant_email', 'applicant_phone', 'prottoyon',
                'format', 'applicant_type_of_businessKhat', 'applicant_type_of_businessKhatAmount',
                'khat'
            ];

            // Extract only the fields that exist in the model from the request
            $dataToUpdate = $request->only($updatableFields);

            // Update the EnglishSonod record (excluding sec_prottoyon for now)
            $englishSonod->update($dataToUpdate);



            if ($request->has('sec_prottoyon')) {
                $englishSonod->update(['sec_prottoyon' => $request->sec_prottoyon]);
            }else{
                // Handle sec_prottoyon update after the main update is completed
                if ($englishSonod->sonod_name == 'বিবিধ প্রত্যয়নপত্র' || $englishSonod->sonod_name == 'অনাপত্তি সনদপত্র') {
                    // Update sec_prottoyon if provided in the request
                    if ($request->has('sec_prottoyon')) {
                        $englishSonod->update(['sec_prottoyon' => $request->sec_prottoyon]);
                    }
                } else {
                    // Generate and update sec_prottoyon for other sonod_name values
                    $englishSonod->update(['sec_prottoyon' => generateSecProttoyon($englishSonod, true)]);
                }
            }



            // Return the updated record in the response
            return response()->json([
                'message' => 'EnglishSonod updated successfully',
                'englishSonod' => $englishSonod
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'EnglishSonod not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Update failed', 'error' => $e->getMessage()], 500);
        }
    }


    public function show(Request $request, $id)
    {
        // Fetch the Sonod record with the english_sonod relationship (only the id)
        $sonod = Sonod::with(['english_sonod' => function ($query) {
            $query->select('id', 'sonod_Id'); // Select only the id and sonod_Id (foreign key)
        }])->find($id);

        // Check if the Sonod record exists
        if (!$sonod) {
            return response()->json(['message' => 'Sonod not found'], 404);
        }

        // Check if the request has the 'en' parameter and if the Sonod has English data
        $en = $request->en; // This will be a string: "true" or "false"
        if ($en === 'true' && $sonod->hasEnData == 1) { // Compare with the string "true"
            // Check if english_sonod exists
            if ($sonod->english_sonod) {
                // Fetch the EnglishSonod record
                $EnglishSonod = EnglishSonod::find($sonod->english_sonod->id);
                return response()->json($EnglishSonod);
            } else {
                // Return a response indicating that no EnglishSonod record exists
                return response()->json(['message' => 'No EnglishSonod record found for this Sonod'], 404);
            }
        }

           // Validate if attachments exist before generating URLs
    $sonod->applicant_national_id_front_attachment = !empty($sonod->applicant_national_id_front_attachment)
    ? getUploadDocumentsToS3($sonod->applicant_national_id_front_attachment)
    : null;

$sonod->applicant_national_id_back_attachment = !empty($sonod->applicant_national_id_back_attachment)
    ? getUploadDocumentsToS3($sonod->applicant_national_id_back_attachment)
    : null;

$sonod->applicant_birth_certificate_attachment = !empty($sonod->applicant_birth_certificate_attachment)
    ? getUploadDocumentsToS3($sonod->applicant_birth_certificate_attachment)
    : null;

        // Return the Sonod record
        return response()->json($sonod);
    }



    function EnglishShow($id){
        $sonod = EnglishSonod::find($id);
        return response()->json($sonod);
    }


}
