<?php

namespace App\Http\Controllers\Api\User\Holdingtax;

use App\Models\Payment;
use App\Models\Holdingtax;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use App\Models\HoldingBokeya;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HoldingtaxController extends Controller
{
    public function store(Request $r)
    {
       // Retrieve 'unioun' from the authenticated user
       $user = auth()->user();
       $unioun = $user->unioun;  // Assuming the 'unioun' field exists on the 'User' model

       // Validation using Validator facade
       $validator = Validator::make($r->all(), [
           'category' => 'required|string',
           'holding_no' => 'required|string',
           'maliker_name' => 'required|string',
           'father_or_samir_name' => 'required|string',
           'gramer_name' => 'required|string',
           'word_no' => 'required|string',
           'nid_no' => 'required|string',
           'mobile_no' => 'required|string',

           'griher_barsikh_mullo' => 'nullable|numeric',
           'jomir_vara' => 'nullable|numeric',
           'barsikh_vara' => 'nullable|numeric',
           'bokeya' => 'array',
           'image' => 'nullable|string',
           'busnessName' => 'nullable|string',
       ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle update case
        if ($r->id) {
            $holdingTax = $this->updateHoldingTax($r);
            return response()->json($holdingTax, 200);
        }

        // Process new holding tax
        $calculationResults = $this->calculateHoldingTax(
            $r->category,
            int_bn_to_en($r->griher_barsikh_mullo ?? 0),
            int_bn_to_en($r->jomir_vara ?? 0),
            int_bn_to_en($r->barsikh_vara ?? 0)
        );

        $currentYearKor = $calculationResults['current_year_kor'];
        // Process bokeya
        $bokeya = is_array($r->bokeya) ? $r->bokeya : []; // Ensure $bokeya is always an array
        $totalBokeya = array_reduce($bokeya, function ($carry, $item) {
            return $carry + $item['price'];
        }, 0);

        $totalBokeya += $currentYearKor;

        // Prepare data for holding tax
        $data = $r->except(['bokeya', 'image']);
        $data['unioun'] = $unioun; // Add 'unioun' from authenticated user
        $data['bokeya'] = json_encode($r->bokeya);
        $data['total_bokeya'] = $totalBokeya;
        $data = array_merge($data, $calculationResults);

        if ($this->isNewImage($r->image)) {
            $data['image'] = $this->uploadImage($r->image);
        }

        $holding = Holdingtax::create($data);
        $this->createHoldingBokeya($holding->id, $calculationResults['current_year_kor']);

       // Handle bokeya entries via a separate function if $r->bokeya is present
       if (!empty($r->bokeya)) {
        $this->handleBokeyaEntries($r->bokeya, $holding->id);
    }


        return response()->json($holding, 201);
    }

    private function calculateHoldingTax($category, $griherBarsikhMullo, $jomirVara, $barsikhVara = null)
    {
        switch ($category) {
            case 'মালিক নিজে বসবাসকারী':
                return $this->calculateOwnerTax($griherBarsikhMullo, $jomirVara);
            case 'প্রতিষ্ঠান':
                return $this->calculateInstitutionTax($griherBarsikhMullo, $jomirVara);
            case 'ভাড়া':
                return $this->calculateRentTax($barsikhVara);
            case 'আংশিক ভাড়া':
                return $this->calculatePartialRentTax($griherBarsikhMullo, $jomirVara, $barsikhVara);
            default:
                // throw new \Exception("Invalid category provided.");
                return response()->json([
                    'message' => "Invalid category provided: '{$category}'.",
                    'valid_categories' => [
                        'মালিক নিজে বসবাসকারী',
                        'প্রতিষ্ঠান',
                        'ভাড়া',
                        'আংশিক ভাড়া'
                    ]
                ], 400);
        }
    }


    private function calculateOwnerTax($griherBarsikhMullo, $jomirVara)
    {
        $barsikhMullerPercent = ($griherBarsikhMullo * 7.5) / 100;
        $totalMullo = $jomirVara + $barsikhMullerPercent;
        $rokhonaBekhonKhoroch = $totalMullo / 6;
        $prakklitoMullo = $totalMullo - $rokhonaBekhonKhoroch;
        $reyad = $prakklitoMullo / 4;
        $prodeyKorjoggoBarsikhMullo = $prakklitoMullo - $reyad;
        $currentYearKor = ($prodeyKorjoggoBarsikhMullo * 7) / 100;

        if ($currentYearKor >= 500) {
            $currentYearKor = 500;
        }

        return [
            'barsikh_muller_percent' => $barsikhMullerPercent,
            'rokhona_bekhon_khoroch_percent' => 0,
            'total_mullo' => $totalMullo,
            'rokhona_bekhon_khoroch' => $rokhonaBekhonKhoroch,
            'prakklito_mullo' => $prakklitoMullo,
            'reyad' => $reyad,
            'prodey_korjoggo_barsikh_mullo' => $prodeyKorjoggoBarsikhMullo,
            'prodey_korjoggo_barsikh_varar_mullo' => 0,
            'angsikh_prodoy_korjoggo_barsikh_mullo' => 0,
            'current_year_kor' => $currentYearKor,
            'total_prodey_korjoggo_barsikh_mullo' => 0,
        ];
    }

    private function calculateInstitutionTax($griherBarsikhMullo, $jomirVara)
    {
        $barsikhMullerPercent = ($griherBarsikhMullo * 7.5) / 100;
        $totalMullo = $jomirVara + $barsikhMullerPercent;
        $rokhonaBekhonKhoroch = $totalMullo / 6;
        $prakklitoMullo = $totalMullo - $rokhonaBekhonKhoroch;
        $reyad = $prakklitoMullo / 4;
        $prodeyKorjoggoBarsikhMullo = $prakklitoMullo - $reyad;
        $currentYearKor = ($prodeyKorjoggoBarsikhMullo * 7) / 100;

        return [
            'barsikh_muller_percent' => $barsikhMullerPercent,
            'rokhona_bekhon_khoroch_percent' => 0,
            'total_mullo' => $totalMullo,
            'rokhona_bekhon_khoroch' => $rokhonaBekhonKhoroch,
            'prakklito_mullo' => $prakklitoMullo,
            'reyad' => $reyad,
            'prodey_korjoggo_barsikh_mullo' => $prodeyKorjoggoBarsikhMullo,
            'prodey_korjoggo_barsikh_varar_mullo' => 0,
            'angsikh_prodoy_korjoggo_barsikh_mullo' => 0,
            'current_year_kor' => $currentYearKor,
            'total_prodey_korjoggo_barsikh_mullo' => 0,
        ];
    }

    private function calculateRentTax($barsikhVara)
    {
        $rokhonaBekhonKhorochPercent = $barsikhVara / 6;
        $prodeyKorjoggoBarsikhVararMullo = $barsikhVara - $rokhonaBekhonKhorochPercent;
        $currentYearKor = ($prodeyKorjoggoBarsikhVararMullo * 7) / 100;

        if ($currentYearKor >= 500) {
            $currentYearKor = 500;
        }

        return [
            'barsikh_muller_percent' => 0,
            'total_mullo' => $barsikhVara,
            'rokhona_bekhon_khoroch_percent' => $rokhonaBekhonKhorochPercent,
            'prodey_korjoggo_barsikh_varar_mullo' => $prodeyKorjoggoBarsikhVararMullo,
            'angsikh_prodoy_korjoggo_barsikh_mullo' => 0,
            'current_year_kor' => $currentYearKor,
            'total_prodey_korjoggo_barsikh_mullo' => 0,
        ];
    }

    private function calculatePartialRentTax($griherBarsikhMullo, $jomirVara, $barsikhVara)
    {
        $barsikhMullerPercent = ($griherBarsikhMullo * 7.5) / 100;
        $totalMullo = $jomirVara + $barsikhMullerPercent;
        $rokhonaBekhonKhoroch = $totalMullo / 6;
        $prakklitoMullo = $totalMullo - $rokhonaBekhonKhoroch;
        $reyad = $prakklitoMullo / 4;
        $angsikhProdoyKorjoggoBarsikhMullo = $prakklitoMullo - $reyad;
        $rokhonaBekhonKhorochPercent = $barsikhVara / 6;
        $prodeyKorjoggoBarsikhVararMullo = $barsikhVara - $rokhonaBekhonKhorochPercent;
        $totalProdeyKorjoggoBarsikhMullo = $angsikhProdoyKorjoggoBarsikhMullo + $prodeyKorjoggoBarsikhVararMullo;
        $currentYearKor = ($totalProdeyKorjoggoBarsikhMullo * 7) / 100;

        return [
            'barsikh_muller_percent' => $barsikhMullerPercent,
            'total_mullo' => $totalMullo,
            'rokhona_bekhon_khoroch' => $rokhonaBekhonKhoroch,
            'prakklito_mullo' => $prakklitoMullo,
            'reyad' => $reyad,
            'angsikh_prodoy_korjoggo_barsikh_mullo' => $angsikhProdoyKorjoggoBarsikhMullo,
            'rokhona_bekhon_khoroch_percent' => $rokhonaBekhonKhorochPercent,
            'prodey_korjoggo_barsikh_varar_mullo' => $prodeyKorjoggoBarsikhVararMullo,
            'current_year_kor' => $currentYearKor,
            'total_prodey_korjoggo_barsikh_mullo' => 0,
        ];
    }

    private function createHoldingBokeya($holdingTaxId, $currentYearKor)
    {
        HoldingBokeya::create([
            'holdingTax_id' => $holdingTaxId,
            'year' => CurrentOrthoBochor(1),
            'price' => $currentYearKor,
            'status' => 'Unpaid',
        ]);
    }

    protected function handleBokeyaEntries(array $bokeyaEntries, int $holdingTaxId)
    {
        foreach ($bokeyaEntries as $entry) {
            $payYear = null;

            $bokeyaData = [
                'holdingTax_id' => $holdingTaxId,
                'year' => $entry['year'],
                'price' => $entry['price'],
                'payYear' => $payYear,
                'status' => 'Unpaid',
            ];
            HoldingBokeya::create($bokeyaData);
        }
    }







    private function updateHoldingTax(Request $r)
    {
        $holding = Holdingtax::findOrFail($r->id);
        $holding->update($r->all());
        return $holding;
    }

    private function isNewImage($image)
    {
        return count(explode(';', $image)) > 1;
    }

    private function uploadImage($image)
    {
        return fileupload($image, "holding/image/", 250, 300);
    }








    public function getSingleHoldingTaxWithBokeyas(Request $r, $id)
    {
        // Get the authenticated user's union
        $auth = Auth::user();
        $userUnion = $r->unioun;
        if ($auth) {
            $userUnion = $auth->unioun;
        }

        // Find the holding tax by its id and eager load the holding bokeyas with specific columns
        $holdingTax = Holdingtax::select(['unioun', 'id', 'holding_no', 'category', 'maliker_name', 'father_or_samir_name', 'gramer_name', 'word_no', 'nid_no', 'mobile_no', 'griher_barsikh_mullo', 'jomir_vara', 'barsikh_vara'])
            ->with(['holdingBokeyas' => function ($query) {
                // Select only required columns for holdingBokeyas and filter out entries with price = 0 or null
                $query->select(['id', 'year', 'price', 'status', 'holdingTax_id']);
                    //   ->whereNotNull('price') // Exclude entries where price is null
                    //   ->where('price', '!=', 0); // Exclude entries where price is 0
            }])
            ->find($id);

        // Check if the holding tax exists
        if (!$holdingTax) {
            return response()->json([
                'message' => 'Holding Tax not found'
            ], 404);
        }

        // Check if the union of the holding tax matches the authenticated user's union
        if ($holdingTax->unioun !== $userUnion && $auth) {
            return response()->json([
                'message' => 'You are not authorized to view this Holding Tax'
            ], 403);
        }

        // Add invoice_url to each holdingBokeya if status is 'Paid'
        foreach ($holdingTax->holdingBokeyas as $bokeya) {
            // Add the invoice URL if status is 'Paid'
            if ($bokeya->status === 'Paid') {
                $bokeya->invoice_url = url('holding/tax/invoice/' . $bokeya->id);
                $bokeya->certificate_of_honor_url = url('holding/tax/certificate_of_honor/' . $bokeya->id);
            } else {
                $bokeya->invoice_url = '';
                $bokeya->certificate_of_honor_url = '';
            }
        }

        return response()->json($holdingTax);
    }





    public function holdingSearch(Request $r)
    {
        // Get the authenticated user's union

        $auth = Auth::user();
        // Get the search parameters from the request
        $search = $r->search;
        $word = $r->word;

        // Query the Holdingtax model and apply filters
        $query = Holdingtax::query();
        $query->select(['id','holding_no','maliker_name','nid_no','mobile_no']);


        $userUnion = $r->unioun;
        if($auth){
            $userUnion = $auth->unioun;
        }
        // Apply union filter based on the authenticated user's union
        $query->where('unioun', $userUnion);

        // Apply search conditions for various fields (holding_no, maliker_name, etc.)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('holding_no', 'like', "%$search%")
                ->orWhere('maliker_name', 'like', "%$search%")
                ->orWhere('nid_no', 'like', "%$search%")
                ->orWhere('mobile_no', 'like', "%$search%");
            });
        }

        // If `word` is provided, apply the word filter
        if ($word) {
            $query->where('word_no', $word);
        }

        // Paginate the results, and return the response
        return response()->json($query->paginate(20));
    }



    public function holding_tax_pay_Online(Request $request,$id)
    {
        $holdingBokeya = HoldingBokeya::find($id);

        if (!$holdingBokeya) {
            return response()->json([
                'message' => 'No data found for the given ID.'
            ], 404);
        }

        $holdingTax = Holdingtax::where(['id' => $holdingBokeya->holdingTax_id])->first();

        if (!$holdingTax) {
            return response()->json([
                'message' => 'No Holding Tax data found for the given ID.'
            ], 404);
        }

        $unioninfos = Uniouninfo::where(['short_name_e' => $holdingTax->unioun])->first();

        if (!$unioninfos) {
            return response()->json([
                'message' => 'No Union Info data found for the given short name.'
            ], 404);
        }

      $u_code = $unioninfos->u_code;

    //   $holdingBokeyasAmount = HoldingBokeya::where(['holdingTax_id'=>$holdingBokeya->holdingTax_id,'status'=>'Unpaid'])->sum('price');

        $trnx_id = $u_code.'-'.time();
        $cust_info = [
            "cust_email" => "",
            "cust_id" => "$holdingBokeya->id",
            "cust_mail_addr" => "Address",
            "cust_mobo_no" => "01909756552",
            "cust_name" => "Customer Name"
        ];

        $trns_info = [
            "ord_det" => 'sonod',
            "ord_id" => "$holdingBokeya->id",
            "trnx_amt" => $holdingBokeya->price,
            "trnx_currency" => "BDT",
            "trnx_id" => "$trnx_id"
        ];

        $urls = [
            "s_uri"=>$request->s_uri,
            "f_uri"=>$request->f_uri,
            "c_uri"=>$request->c_uri
        ];

        $redirectutl = ekpayToken($trnx_id, $trns_info, $cust_info,'payment',$holdingTax->unioun,$urls);


        $req_timestamp = date('Y-m-d H:i:s');
        $customerData = [
            'union' => $holdingTax->unioun,
            'trxId' => $trnx_id,
            'transaction_id' => $trnx_id,
            'sonodId' => $id,
            'sonod_type' => 'holdingtax',
            'amount' => $holdingBokeya->price,
            'mob' => "01909756552",
            'status' => "Pending",
            'paymentUrl' => $redirectutl,
            'method' => 'ekpay',
            'payment_type' => 'online',
            'date' => date('Y-m-d'),
            'created_at' => $req_timestamp,
            'gateway' => 'upcoming',
        ];
        Payment::create($customerData);

        //  $redirectutl =  ekpayToken($trnx_id, $holdingBokeya->price, $cust_info,'holdingPay');


        return response()->json($redirectutl);


    }



    public function updateUnpaidHoldingBokeyaPrice(Request $request, $id)
    {
        // Find the HoldingBokeya entry by ID
        $holdingBokeya = HoldingBokeya::find($id);

        // Check if the HoldingBokeya entry exists
        if (!$holdingBokeya) {
            return response()->json([
                'message' => 'Holding Bokeya not found'
            ], 404);
        }



        // Get the authenticated user
        $user = auth()->user();

        // Get the associated Holdingtax record
        $holdingTax = $holdingBokeya->holdingTax;

        // Check if the authenticated user's unioun matches the Holdingtax unioun
        if ($user->unioun !== $holdingTax->unioun) {
            return response()->json([
                'message' => 'You are not authorized to update this Holding Bokeya'
            ], 403);
        }


        // Ensure the status is 'Unpaid'
        if ($holdingBokeya->status !== 'Unpaid') {
            return response()->json([
                'message' => 'Only Holding Bokeya with status "Unpaid" can be updated'
            ], 400);
        }


        // Validate the request data (only 'price' is allowed)
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric|min:0', // Ensure price is a valid number
        ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update only the price of the HoldingBokeya entry
        $holdingBokeya->price = $request->price;
        $holdingBokeya->save();

        return response()->json([
            'message' => 'Holding Bokeya price updated successfully',
            'data' => $holdingBokeya
        ], 200);
    }




    public function updateHoldingtaxOnly(Request $request, $id)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'category' => 'sometimes|required|string',
            'holding_no' => 'sometimes|required|string',
            'maliker_name' => 'sometimes|required|string',
            'father_or_samir_name' => 'sometimes|required|string',
            'gramer_name' => 'sometimes|required|string',
            'word_no' => 'sometimes|required|string',
            'nid_no' => 'sometimes|required|string',
            'mobile_no' => 'sometimes|required|string',
            'griher_barsikh_mullo' => 'sometimes|nullable|numeric',
            'jomir_vara' => 'sometimes|nullable|numeric',
            'barsikh_vara' => 'sometimes|nullable|numeric',
            'image' => 'sometimes|nullable|string',
            'busnessName' => 'sometimes|nullable|string',
        ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the Holdingtax record by ID
        $holdingTax = Holdingtax::find($id);

        // Check if the Holdingtax record exists
        if (!$holdingTax) {
            return response()->json([
                'message' => 'Holding Tax not found'
            ], 404);
        }

        // Get the authenticated user
        $user = auth()->user();

        // Check if the authenticated user's unioun matches the Holdingtax unioun
        if ($user->unioun !== $holdingTax->unioun) {
            return response()->json([
                'message' => 'You are not authorized to update this Holding Tax'
            ], 403);
        }

        // Update the Holdingtax record with the validated data
        $holdingTax->update($request->all());

        // Handle image update if a new image is provided
        if ($request->has('image') && $this->isNewImage($request->image)) {
            $holdingTax->image = $this->uploadImage($request->image);
            $holdingTax->save();
        }

        return response()->json([
            'message' => 'Holding Tax updated successfully',
            'data' => $holdingTax
        ], 200);
    }


     /**
     * Add a new bokeya entry for a specific Holdingtax record.
     *
     * @param Request $request
     * @param int $holdingTaxId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addNewBokeya(Request $request, $holdingTaxId)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'year' => 'required|string', // Year of the bokeya
            'price' => 'required|numeric|min:0', // Price of the bokeya
        ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the Holdingtax record by ID
        $holdingTax = Holdingtax::find($holdingTaxId);

        // Check if the Holdingtax record exists
        if (!$holdingTax) {
            return response()->json([
                'message' => 'Holding Tax not found'
            ], 404);
        }

        // Get the authenticated user
        $user = auth()->user();

        // Check if the authenticated user's unioun matches the Holdingtax unioun
        if ($user->unioun !== $holdingTax->unioun) {
            return response()->json([
                'message' => 'You are not authorized to add a bokeya for this Holding Tax'
            ], 403);
        }

        // Create a new bokeya entry
        $bokeyaData = [
            'holdingTax_id' => $holdingTaxId,
            'year' => $request->year,
            'price' => $request->price,
            'status' => 'Unpaid',
        ];

        $bokeya = HoldingBokeya::create($bokeyaData);

        return response()->json([
            'message' => 'Bokeya added successfully',
            'data' => $bokeya
        ], 201);
    }


}
