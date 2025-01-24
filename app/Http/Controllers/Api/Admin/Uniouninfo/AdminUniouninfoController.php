<?php
namespace App\Http\Controllers\Api\Admin\Uniouninfo;

use App\Models\User;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Models\AllowedOrigin;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Return_;

class AdminUniouninfoController extends Controller
{
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
            'AKPAY_MER_REG_ID' => $data['AKPAY_MER_REG_ID'] ?? "",
            'AKPAY_MER_PASS_KEY' => $data['AKPAY_MER_PASS_KEY'] ?? "",
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



        // Get the unions for the Upazila
        $unions = $Upazila->unions;

        foreach ($unions as $union) {
            // Generate the union name in lowercase and without spaces
            $unionName = str_replace(' ', '', strtolower($union->name));

            // Prepare the data for Uniouninfo
            $data = [
                'full_name' => "$union->bn_name ইউনিয়ন পরিষদ",
                'short_name_e' => $unionName,
                'short_name_b' => $union->bn_name,
                'thana' => $Upazila->bn_name,
                'district' => $Upazila->district->bn_name,
                'c_type' => "চেয়ারম্যান",
                'c_type_en' => "Chairman",
                'u_code' => uniqid(),
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



    public function getUniouninfoByUpazila($upazilaId)
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
        $formattedUniouninfoList = $uniouninfoList->map(function ($uniouninfo) use ($upazila) {
            return [
                'id' => $uniouninfo->id,
                'full_name' => $uniouninfo->full_name,
                'short_name_e' => $uniouninfo->short_name_e,
                'short_name_b' => $uniouninfo->short_name_b,
                'thana' => $uniouninfo->thana,
                'district' => $uniouninfo->district,
                'c_type' => $uniouninfo->c_type,
                'c_type_en' => $uniouninfo->c_type_en,
                'u_code' => $uniouninfo->u_code,
                'defaultColor' => $uniouninfo->defaultColor,
                'payment_type' => $uniouninfo->payment_type,
                'full_name_en' => $uniouninfo->full_name_en,
                'district_en' => $uniouninfo->district_en,
                'thana_en' => $uniouninfo->thana_en,
                'upazila_name' => $upazila->name,
                'upazila_bn_name' => $upazila->bn_name,
                'district_name' => $upazila->district->name,
                'district_bn_name' => $upazila->district->bn_name,
                'division_name' => $upazila->district->division->name,
                'division_bn_name' => $upazila->district->division->bn_name,
            ];
        });

        // Return the formatted Uniouninfo list
        return response()->json($formattedUniouninfoList, 200);
    }



}
