<?php
namespace App\Http\Controllers\Api\Admin\Uniouninfo;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Payment;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use App\Models\AllowedOrigin;
use PhpParser\Node\Stmt\Return_;
use App\Exports\UniouninfoExport;
use App\Models\EkpayPaymentReport;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Devfaysal\BangladeshGeocode\Models\Upazila;

class AdminUniouninfoController extends Controller
{


    public function getAllWithPhones()
    {
        $uniouninfos = Uniouninfo::select('full_name','thana','district','chairman_phone', 'secretary_phone', 'udc_phone', 'user_phone')->whereNotNull('chairman_phone')
            ->orWhereNotNull('secretary_phone')
            ->orWhereNotNull('udc_phone')
            ->orWhereNotNull('user_phone')
            ->get();

        if ($uniouninfos->isEmpty()) {
            return response()->json(['message' => 'No Uniouninfo records with phone numbers found'], 404);
        }

        return response()->json(['uniouninfos' => $uniouninfos], 200);
    }



    /**
     * Display the specified union info.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Find the Union information by ID
        $unionInfo = Uniouninfo::find($id);

        // If Union information is not found, return a 404 response
        if (!$unionInfo) {
            return response()->json([
                'message' => 'Union information not found.',
            ], 404);
        }

        // Replace file path columns with full URLs using the /files/{path} route
        $fileFields = ['web_logo', 'sonod_logo', 'c_signture', 'socib_signture'];

        foreach ($fileFields as $field) {
            if ($unionInfo->$field) {
                try {
                    // Replace the file path with the full URL
                    $unionInfo->$field = URL::to('/files/' . $unionInfo->$field);
                } catch (\Exception $e) {
                    // If the file is not found or cannot be read, set the value to null
                    $unionInfo->$field = null;
                }
            } else {
                // If the field is empty, set the value to null
                $unionInfo->$field = null;
            }
        }

        // Return the response with the Union information and updated file URLs
        return response()->json([
            'message' => 'Union information retrieved successfully.',
            'data' => $unionInfo,
        ], 200);
    }

    /**
     * Store a newly created union info.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'full_name' => 'required|string',
            'short_name_e' => 'required|string',
            'domain' => 'nullable|string',
            'portal' => 'nullable|string',
            'short_name_b' => 'nullable|string',
            'thana' => 'nullable|string',
            'district' => 'nullable|string',
            'web_logo' => 'nullable|file|image|max:2048',
            'sonod_logo' => 'nullable|file|image|max:2048',
            'c_signture' => 'nullable|file|image|max:2048',
            'socib_signture' => 'nullable|file|image|max:2048',
            'u_image' => 'nullable|file|image|max:2048',
            'c_name' => 'nullable|string',
            'c_type' => 'nullable|string',
            'c_email' => 'nullable|email',
            'socib_name' => 'nullable|string',
            'socib_email' => 'nullable|email',
            'u_description' => 'nullable|string',
            'u_notice' => 'nullable|string',
            'u_code' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'google_map' => 'nullable|string',
            'defaultColor' => 'nullable|string',
            'payment_type' => 'nullable|string',
            'AKPAY_MER_REG_ID' => 'nullable|string',
            'AKPAY_MER_PASS_KEY' => 'nullable|string',
            'smsBalance' => 'nullable|numeric',
            'nidServicestatus' => 'nullable|string',
            'nidService' => 'nullable|string',
            'status' => 'required|boolean',
            'type' => 'required|string',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return the errors
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create new union info record
        $unionInfo = new Uniouninfo($request->only(array_keys($rules)));
        $unionInfo->save();
        // Handle file uploads using the saveFile method
        if ($request->hasFile('web_logo')) {
            $unionInfo->saveFile($request->file('web_logo'), 'web_logo', 'web_logo');
        }
        if ($request->hasFile('sonod_logo')) {
            $unionInfo->saveFile($request->file('sonod_logo'), 'sonod_logo', 'sonod_logo');
        }
        if ($request->hasFile('c_signture')) {
            $unionInfo->saveFile($request->file('c_signture'), 'c_signture', 'c_signture');
        }
        if ($request->hasFile('socib_signture')) {
            $unionInfo->saveFile($request->file('socib_signture'), 'socib_signture', 'socib_signture');
        }
        if ($request->hasFile('u_image')) {
            $unionInfo->saveFile($request->file('u_image'), 'u_image', 'u_image');
        }



        return response()->json([
            'message' => 'Union information created successfully.',
            'data' => $unionInfo,
        ], 201);
    }

    /**
     * Update the specified union info.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $unionInfo = Uniouninfo::find($id);

        if (!$unionInfo) {
            return response()->json([
                'message' => 'Union information not found.',
            ], 404);
        }

        // Validation rules
        $rules = [
            'full_name' => 'nullable|string',
            'short_name_e' => 'nullable|string',
            'domain' => 'nullable|string',
            'portal' => 'nullable|string',
            'short_name_b' => 'nullable|string',
            'thana' => 'nullable|string',
            'district' => 'nullable|string',
            'web_logo' => 'nullable|file|image|max:2048',
            'sonod_logo' => 'nullable|file|image|max:2048',
            'c_signture' => 'nullable|file|image|max:2048',
            'socib_signture' => 'nullable|file|image|max:2048',
            'u_image' => 'nullable|file|image|max:2048',
            'c_name' => 'nullable|string',
            'c_type' => 'nullable|string',
            'c_email' => 'nullable|email',
            'socib_name' => 'nullable|string',
            'socib_email' => 'nullable|email',
            'u_description' => 'nullable|string',
            'u_notice' => 'nullable|string',
            'u_code' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'google_map' => 'nullable|string',
            'defaultColor' => 'nullable|string',
            'payment_type' => 'nullable|string',
            'AKPAY_MER_REG_ID' => 'nullable|string',
            'AKPAY_MER_PASS_KEY' => 'nullable|string',
            'smsBalance' => 'nullable|numeric',
            'nidServicestatus' => 'nullable|string',
            'nidService' => 'nullable|string',
            'status' => 'nullable|boolean',
            'type' => 'nullable|string',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return the errors
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

          // Update the union info with validated data
          $unionInfo->update($request->only(array_keys($rules)));
        // Handle file uploads
        if ($request->hasFile('web_logo')) {
            $unionInfo->saveFile($request->file('web_logo'), 'web_logo', 'web_logo');
        }
        if ($request->hasFile('sonod_logo')) {
            $unionInfo->saveFile($request->file('sonod_logo'), 'sonod_logo', 'sonod_logo');
        }
        if ($request->hasFile('c_signture')) {
            $unionInfo->saveFile($request->file('c_signture'), 'c_signture', 'c_signture');
        }
        if ($request->hasFile('socib_signture')) {
            $unionInfo->saveFile($request->file('socib_signture'), 'socib_signture', 'socib_signture');
        }
        if ($request->hasFile('u_image')) {
            $unionInfo->saveFile($request->file('u_image'), 'u_image', 'u_image');
        }



        return response()->json([
            'message' => 'Union information updated successfully.',
            'data' => $unionInfo,
        ], 200);
    }



    public function createUnionWithUsers(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'full_name' => 'nullable|string',
            'short_name_e' => 'nullable|string',
            'domain' => 'nullable|string',
            'portal' => 'nullable|string',
            'short_name_b' => 'nullable|string',
            'thana' => 'nullable|string',
            'district' => 'nullable|string',
            'web_logo' => 'nullable|file',
            'sonod_logo' => 'nullable|file',
            'c_signture' => 'nullable|file',
            'c_name' => 'nullable|string',
            'c_type' => 'nullable|string',
            'c_type_en' => 'nullable|string',
            'c_email' => 'nullable|email',
            'socib_name' => 'nullable|string',
            'socib_signture' => 'nullable|file',
            'socib_email' => 'nullable|email',
            'format' => 'nullable|string',
            'u_image' => 'nullable|file',
            'u_description' => 'nullable|string',
            'u_notice' => 'nullable|string',
            'u_code' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'google_map' => 'nullable|string',
            'defaultColor' => 'nullable|string',
            'payment_type' => 'nullable|string',
            'AKPAY_MER_REG_ID' => 'nullable|string',
            'AKPAY_MER_PASS_KEY' => 'nullable|string',
            'smsBalance' => 'nullable|integer',
            'nidServicestatus' => 'nullable|string',
            'nidService' => 'nullable|string',
            'status' => 'nullable|string',
            'type' => 'nullable|string',
            'full_name_en' => 'nullable|string',
            'c_name_en' => 'nullable|string',
            'district_en' => 'nullable|string',
            'thana_en' => 'nullable|string',
            'socib_name_en' => 'nullable|string',

            'chairman_name' => 'nullable|string',
            'chairman_email' => 'nullable|email',
            'chairman_phone' => 'nullable|string',
            'chairman_password' => 'nullable|string',

            'secretary_name' => 'nullable|string',
            'secretary_email' => 'nullable|email',
            'secretary_phone' => 'nullable|string',
            'secretary_password' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Convert request data to an array
        $data = $request->all();

        // Create the Uniouninfo record
        $uniouninfo = $this->createUniouninfo($data);

        // Create the Chairman User
        $chairman = $this->createChairman($data);

        // Create the Secretary User
        $secretary = $this->createSecretary($data);

        // Create allowed origins
        $this->createAllowedOrigins($data['short_name_e']);

        // Handle file uploads if necessary
        $this->handleFileUploads($data, $uniouninfo);

        // Return a success response
        return response()->json([
            'message' => 'Union and users created successfully',
            'uniouninfo' => $uniouninfo,
            'chairman' => $chairman,
            'secretary' => $secretary,
        ], 201);
    }

    /**
     * Create Uniouninfo record.
     */
    protected function createUniouninfo(array $data)
    {
        return Uniouninfo::create([
            'full_name' => $data['full_name'] ?? "",
            'short_name_e' => $data['short_name_e'] ?? null,
            'domain' => $data['domain'] ?? "",
            'portal' => $data['portal'] ?? "",
            'short_name_b' => $data['short_name_b'] ?? "",
            'thana' => $data['thana'] ?? "",
            'district' => $data['district'] ?? "",
            'web_logo' => $data['web_logo'] ?? "",
            'sonod_logo' => $data['sonod_logo'] ?? "",
            'c_signture' => $data['c_signture'] ?? "",
            'c_name' => $data['c_name'] ?? "",
            'c_type' => $data['c_type'] ?? "",
            'c_type_en' => $data['c_type_en'] ?? "",
            'c_email' => $data['c_email'] ?? "",
            'socib_name' => $data['socib_name'] ?? "",
            'socib_signture' => $data['socib_signture'] ?? "",
            'socib_email' => $data['socib_email'] ?? "",
            'format' => $data['format'] ?? 2,
            'u_image' => $data['u_image'] ?? "",
            'u_description' => $data['u_description'] ?? "",
            'u_notice' => $data['u_notice'] ?? "",
            'u_code' => $data['u_code'] ?? "",
            'contact_email' => $data['contact_email'] ?? "",
            'google_map' => $data['google_map'] ?? "",
            'defaultColor' => $data['defaultColor'] ?? "",
            'payment_type' => $data['payment_type'] ?? "",
            'AKPAY_MER_REG_ID' => $data['AKPAY_MER_REG_ID'] ?? "tetulia_test",
            'AKPAY_MER_PASS_KEY' => $data['AKPAY_MER_PASS_KEY'] ?? "TetuLiA@tsT19",
            'smsBalance' => $data['smsBalance'] ?? 0,
            'nidServicestatus' => $data['nidServicestatus'] ?? 0,
            'nidService' => $data['nidService'] ?? 0,
            'status' => $data['status'] ?? 0,
            'type' => $data['type'] ?? "union",
            'full_name_en' => $data['full_name_en'] ?? "",
            'c_name_en' => $data['c_name_en'] ?? "",
            'district_en' => $data['district_en'] ?? "",
            'thana_en' => $data['thana_en'] ?? "",
            'socib_name_en' => $data['socib_name_en'] ?? "",
        ]);
    }

    /**
     * Create Chairman User.
     */
    protected function createChairman(array $data)
    {
        return User::create([
            'unioun' => $data['short_name_e'] ?? null,
            'names' => $data['chairman_name'] ?? "চেয়ারম্যানের নাম",
            'email' => $data['chairman_email'] ?? "upc{$data['short_name_e']}@gmail.com",
            'phone' => $data['chairman_phone'] ?? "01909756552",
            'password' => bcrypt($data['chairman_password'] ?? "upsheba21"),
            'position' => 'Chairman',
            'role' => 'Chairman',
        ]);
    }

    /**
     * Create Secretary User.
     */
    protected function createSecretary(array $data)
    {
        return User::create([
            'unioun' => $data['short_name_e'] ?? null,
            'names' => $data['secretary_name'] ?? "সেক্রেটারির নাম",
            'email' => $data['secretary_email'] ?? "ups{$data['short_name_e']}@gmail.com",
            'phone' => $data['secretary_phone'] ?? "01909756552",
            'password' => bcrypt($data['secretary_password'] ?? "upsheba21"),
            'position' => 'Secretary',
            'role' => 'Secretary',
        ]);
    }

    /**
     * Create Allowed Origins.
     */
    protected function createAllowedOrigins($shortNameE)
    {
        $origin_url1 = "https://$shortNameE.uniontax.gov.bd";
        AllowedOrigin::create(['origin_url' => $origin_url1]);

        $origin_url2 = "https://$shortNameE.unionservices.gov.bd";
        AllowedOrigin::create(['origin_url' => $origin_url2]);
    }

    /**
     * Handle file uploads.
     */
    protected function handleFileUploads(array $data, $uniouninfo)
    {
        if (isset($data['web_logo']) && $data['web_logo'] instanceof \Illuminate\Http\UploadedFile) {
            $uniouninfo->saveFile($data['web_logo'], 'web_logo');
        }
        if (isset($data['sonod_logo']) && $data['sonod_logo'] instanceof \Illuminate\Http\UploadedFile) {
            $uniouninfo->saveFile($data['sonod_logo'], 'sonod_logo');
        }
        if (isset($data['c_signture']) && $data['c_signture'] instanceof \Illuminate\Http\UploadedFile) {
            $uniouninfo->saveFile($data['c_signture'], 'c_signture');
        }
        if (isset($data['socib_signture']) && $data['socib_signture'] instanceof \Illuminate\Http\UploadedFile) {
            $uniouninfo->saveFile($data['socib_signture'], 'socib_signture');
        }
        if (isset($data['u_image']) && $data['u_image'] instanceof \Illuminate\Http\UploadedFile) {
            $uniouninfo->saveFile($data['u_image'], 'u_image');
        }
    }




    function createUnion(Request $request, $id) {
        // Fetch the Upazila with its related data
        $Upazila = Upazila::with('district.division', 'unions')->find($id);

        if (!$Upazila) {
            return response()->json([
                'message' => 'Upazila not found',
            ], 404);
        }

        // Read the JSON file
        $fileContent = Storage::disk('protected')->get('unionList-127-145-RANGPUR.json');
        $jsonData = json_decode($fileContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'error' => 'Invalid JSON file',
            ], 400);
        }

        // Get the union names and codes from the JSON data for the specific upazila
        $unionCodes = [];
        $jsonUpazilaNames = [];
        $matchedUpazilaName = null;

        foreach ($jsonData['zilas'] as $zila) {
            foreach ($zila['upazilas'] as $upazilaData) {
                $jsonUpazilaNames[] = $upazilaData['name']; // Collect all upazila names from JSON
                if (strtolower($upazilaData['name']) === strtolower($Upazila->name)) {
                    $matchedUpazilaName = $upazilaData['name']; // Store the matched upazila name
                    foreach ($upazilaData['unions'] as $union) {
                        $unionCodes[strtolower(str_replace(' ', '', $union['name']))] = $union['code'];
                    }
                    break 2; // Exit both loops once the upazila is found
                }
            }
        }

        // return $unionCodes;
        // If no union codes were found, return the Upazila name and JSON upazila names
        if (empty($unionCodes)) {
            return response()->json([
                'message' => 'No matching upazila found in JSON data',
                'upazila_name' => $Upazila->name,
                'json_upazila_names' => $jsonUpazilaNames,
            ], 404);
        }

        // Get the unions for the Upazila
        $unions = $Upazila->unions;

        foreach ($unions as $union) {
            // Generate the union name in lowercase and without spaces
            $unionName = str_replace(' ', '', strtolower($union->name));
            $unionCode = $unionCodes[$unionName] ?? uniqid();

            // Prepare the data for Uniouninfo
            $data = [
                'full_name' => "$union->bn_name ইউনিয়ন পরিষদ",
                'short_name_e' => $unionName,
                'short_name_b' => $union->bn_name,
                'thana' => $Upazila->bn_name,
                'district' => $Upazila->district->bn_name,
                'c_type' => isUnion() ? "চেয়ারম্যান" : "প্রশাসক",
                'c_type_en' => "Chairman",
                'u_code' => $unionCode,
                "defaultColor" => "green",
                "payment_type" => "Prepaid",
                "full_name_en" => "$union->name union porisod",
                "district_en" => $Upazila->district->name,
                "thana_en" => $Upazila->name,
            ];

            // Check if a Uniouninfo record with the same short_name_e already exists
            $existingUniouninfo = Uniouninfo::where('short_name_e', $data['short_name_e'])->first();

            if ($existingUniouninfo) {
                // If it exists, skip this union and continue to the next one
                continue;
            }

            // Create the Uniouninfo record
            $uniouninfo = $this->createUniouninfo($data);

            // Create the Chairman User
            $chairman = $this->createChairman($data);

            // Create the Secretary User
            $secretary = $this->createSecretary($data);

            // Create allowed origins
            $this->createAllowedOrigins($data['short_name_e']);
        }

        // Return a success response
        return response()->json([
            'message' => 'Unions and users created successfully',
        ], 201);
    }



    public function getUniouninfoByUpazila(Request $request, $upazilaId)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Fetch the Upazila with its related unions
        $upazila = Upazila::with('unions')->find($upazilaId);

        if (!$upazila) {
            return response()->json(['message' => 'Upazila not found'], 404);
        }

        // Get union short names (transformed)
        $unionNames = $upazila->unions->pluck('name')->map(function ($name) {
            return str_replace(' ', '', strtolower($name)); // Remove spaces and lowercase
        })->toArray();

        // Fetch unioninfo records where short_name_e matches
        $uniouninfoList = Uniouninfo::whereIn('short_name_e', $unionNames)->get();

        // Format response
        $formattedUniouninfoList = $uniouninfoList->map(function ($uniouninfo) use ($startDate, $endDate) {
            $report = null;
            $serverAmount = null;

            // Apply Ekpay report only if both start & end dates are provided
            if ($startDate && $endDate) {
                $report = EkpayPaymentReport::where('union', $uniouninfo->short_name_e)
                    ->where('start_date', $startDate)
                    ->where('end_date', $endDate)
                    ->first();
            }

            // Always calculate server amount using Payment model with default 7-day window
            $from = $startDate ?: Carbon::now()->subDays(7)->toDateString();
            $to = $endDate ?: Carbon::now()->toDateString();
            

            $query = Payment::where('union', $uniouninfo->short_name_e);

            if ($from === $to) {
                $query->whereDate('date', $from);
            } else {
                $query->whereDate('date', '>=', $from)
                      ->whereDate('date', '<=', $to);
            }

            $serverAmount = $query->sum('amount');

            return [
                'id' => $uniouninfo->id,
                'full_name' => $uniouninfo->full_name,
                'short_name_e' => $uniouninfo->short_name_e,
                'thana' => $uniouninfo->thana,
                'district' => $uniouninfo->district,
                'u_code' => $uniouninfo->u_code,
                'AKPAY_MER_REG_ID' => $uniouninfo->AKPAY_MER_REG_ID,
                'AKPAY_MER_PASS_KEY' => $uniouninfo->AKPAY_MER_PASS_KEY,
                'chairman_phone' => $uniouninfo->chairman_phone,
                'secretary_phone' => $uniouninfo->secretary_phone,
                'udc_phone' => $uniouninfo->udc_phone,
                'user_phone' => $uniouninfo->user_phone,
                'ekpay_amount' => $report?->ekpay_amount,
                'server_amount' => $serverAmount,
                'difference_amount' => $report && $serverAmount !== null
                    ? floatval($report->ekpay_amount) - floatval($serverAmount)
                    : null,
            ];
        });

        return response()->json($formattedUniouninfoList, 200);
    }





    public function getUniouninfoByUpazilaAndGenaratePdf($upazilaId)
    {
        // Fetch the Upazila with its related unions
        $upazila = Upazila::with('unions')->find($upazilaId);

        if (!$upazila) {
            return response()->json([
                'message' => 'Upazila not found',
            ], 404);
        }

        // Get the union names from the Upazila and transform them
        $unionNames = $upazila->unions->pluck('name')->map(function ($name) {
            return str_replace(' ', '', strtolower($name)); // Remove spaces and convert to lowercase
        })->toArray();

        // Fetch Uniouninfo records where short_name_e matches the transformed union names
        $uniouninfoList = Uniouninfo::whereIn('short_name_e', $unionNames)->get();

        // Format the Uniouninfo data
        $formattedUniouninfoList = $uniouninfoList->map(function ($uniouninfo) {

            $unioun = $uniouninfo->short_name_e;
            $Secretary = User::where(['unioun' => $unioun, 'position' => 'Secretary'])->first();
            $Chairman = User::where(['unioun' => $unioun, 'position' => 'Chairman'])->first();

            return [
                'id' => $uniouninfo->id,
                'full_name' => $uniouninfo->full_name,
                'thana' => $uniouninfo->thana,
                'district' => $uniouninfo->district,
                'url1' => "https://$unioun.uniontax.gov.bd",
                'url2' => "https://$unioun.unionservices.gov.bd",
                'Secretary_email' => $Secretary->email,
                'Secretary_password' => 'upsheba21',
                'Chairman_email' => $Chairman->email,
                'Chairman_password' => 'upsheba21',
                'u_code' => $uniouninfo->u_code,
                'AKPAY_MER_REG_ID' => $uniouninfo->AKPAY_MER_REG_ID,
                'AKPAY_MER_PASS_KEY' => $uniouninfo->AKPAY_MER_PASS_KEY,
            ];
        });

        // Render the Blade view and pass the data, including district and thana from the Upazila model
        $html = View::make('pdf.union_info', [
            'uniouninfoList' => $formattedUniouninfoList,
            'district' => $upazila->district,  // Pass district
            'thana' => $upazila->bn_name       // Pass thana
        ])->render();

        $district_bn_name = $upazila->district->bn_name;
        $pdfname = "$district_bn_name-জেলার-$upazila->bn_name-উপজেলার সকল ইউনিয়ন এর ওয়েবসাইট এবং ইমেইল পাসওয়ার্ড.pdf";
        // Call the generatePdf function to create the PDF
        generatePdf($html, null, null, "$pdfname",'A4','bangla');
    }


    public function getUniouninfoByUpazilaAndGenarateExcel($upazilaId)
    {
        $upazila = Upazila::with('unions')->find($upazilaId);

        if (!$upazila) {
            return response()->json([
                'message' => 'Upazila not found',
            ], 404);
        }

        $districtShort = strtolower(substr($upazila->district->name, 0, 3)); // First 3 letters of district (lowercase)
        $upazilaShort = strtolower(substr($upazila->name, 0, 3)); // First 3 letters of upazila (lowercase)

        $unionNames = $upazila->unions->pluck('name')->map(function ($name) {
            return strtolower(str_replace(' ', '', $name));
        })->toArray();

        $uniouninfoList = Uniouninfo::whereIn('short_name_e', $unionNames)->get();

        $serverIp = $_SERVER['SERVER_ADDR'] ?? '';
        $formattedUniouninfoList = $uniouninfoList->map(function ($uniouninfo) use ($districtShort, $upazilaShort,$serverIp,$upazila) {
            $unioun = strtolower($uniouninfo->short_name_e); // Ensure lowercase
            $Secretary = User::where(['unioun' => $unioun, 'position' => 'Secretary'])->first();

            $merchant_id = ($uniouninfo->AKPAY_MER_REG_ID ?? '') === 'tetulia_test'
            ? "{$districtShort}_{$upazilaShort}_{$unioun}_up"
            : ($uniouninfo->AKPAY_MER_REG_ID ?? "{$districtShort}_{$upazilaShort}_{$unioun}_up");

            $pass = ($uniouninfo->AKPAY_MER_REG_ID ?? '') === 'tetulia_test'
            ? ""
            : ($uniouninfo->AKPAY_MER_PASS_KEY ?? "");



            return [
                'merchant_id' => $merchant_id,
                'pass' => $pass,
                'organization' => "{$unioun} UP, {$upazila->name}, {$upazila->district->name}",
                'ip' => $serverIp, // Assuming you have a server_ip column
                'mobile' => int_bn_to_en(optional($Secretary)->phone) ?? '',
                'url' => "https://$unioun.uniontax.gov.bd",
            ];
        });

        $district_bn_name = $upazila->district->bn_name;
        $fileName = "Ekpay Credentials-".strtolower("$district_bn_name-জেলার-$upazila->bn_name-উপজেলার-ইউনিয়ন.xlsx");


        // return response()->json($formattedUniouninfoList);

        return Excel::download(new UniouninfoExport($formattedUniouninfoList), $fileName);
    }




    public function CreateUniouninfoContactByUpazila($upazilaId)
    {
        // Fetch the Upazila with its related unions and district
        $upazila = Upazila::with(['unions', 'district'])->find($upazilaId);

        if (!$upazila) {
            return response()->json([
                'message' => 'Upazila not found',
            ], 404);
        }

        // Get the union names from the Upazila and transform them
        $unionNames = $upazila->unions->pluck('name')->map(function ($name) {
            return str_replace(' ', '', strtolower($name));
        })->toArray();

        // Fetch Uniouninfo records
        $uniouninfoList = Uniouninfo::whereIn('short_name_e', $unionNames)->get();

        $contacts = [];

        foreach ($uniouninfoList as $uniouninfo) {
            // Base name structure without role
            $baseName = sprintf(
                "%s - %s - %s (%s)",
                $upazila->district->name ?? 'Unknown District',
                $upazila->name,
                $uniouninfo->short_name_e ?? 'Unknown Union',
                $uniouninfo->full_name
            );

            // Add contacts with their specific roles
            if (!empty($uniouninfo->chairman_phone)) {
                $contacts[] = [
                    'name' => 'Chairman - ' . $baseName,
                    'phone_number' => $uniouninfo->chairman_phone
                ];
            }

            if (!empty($uniouninfo->secretary_phone)) {
                $contacts[] = [
                    'name' => 'Secretary - ' . $baseName,
                    'phone_number' => $uniouninfo->secretary_phone
                ];
            }

            if (!empty($uniouninfo->udc_phone)) {
                $contacts[] = [
                    'name' => 'UDC - ' . $baseName,
                    'phone_number' => $uniouninfo->udc_phone
                ];
            }

            if (!empty($uniouninfo->user_phone)) {
                $contacts[] = [
                    'name' => 'User - ' . $baseName,
                    'phone_number' => $uniouninfo->user_phone
                ];
            }
        }
        $savedcontacts = addOrUpdateContacts($contacts);

        return response()->json(['contacts'=>$contacts,'savedcontacts'=>$savedcontacts], 200);
    }



}
