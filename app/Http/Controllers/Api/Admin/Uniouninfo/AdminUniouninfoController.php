<?php
namespace App\Http\Controllers\Api\Admin\Uniouninfo;

use App\Models\User;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Models\AllowedOrigin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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


            'chairman_name' => 'required|string',
            'chairman_email' => 'required|email',
            'chairman_phone' => 'required|string',
            'chairman_password' => 'required|string',

            'secretary_name' => 'required|string',
            'secretary_email' => 'required|email',
            'secretary_phone' => 'required|string',
            'secretary_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the Uniouninfo record
        $uniouninfo = Uniouninfo::create($request->only([
            'full_name', 'short_name_e', 'domain', 'portal', 'short_name_b', 'thana', 'district',
            'web_logo', 'sonod_logo', 'c_signture', 'c_name', 'c_type', 'c_type_en', 'c_email',
            'socib_name', 'socib_signture', 'socib_email', 'format', 'u_image', 'u_description',
            'u_notice', 'u_code', 'contact_email', 'google_map', 'defaultColor', 'payment_type',
            'AKPAY_MER_REG_ID', 'AKPAY_MER_PASS_KEY', 'smsBalance', 'nidServicestatus', 'nidService',
            'status', 'type', 'full_name_en', 'c_name_en', 'district_en', 'thana_en', 'socib_name_en',
        ]));

        // Create the Chairman User
        $chairman = User::create([
            'unioun' => $request->short_name_e,
            'names' => $request->chairman_name,
            'email' => $request->chairman_email,
            'phone' => $request->chairman_phone,
            'password' => bcrypt($request->chairman_password),
            'position' => 'Chairman',
            'unioun' => $uniouninfo->id,
            'role' => 'Chairman', // Assuming you have a role field
        ]);

        // Create the Secretary User
        $secretary = User::create([
            'unioun' => $request->short_name_e,
            'names' => $request->secretary_name,
            'email' => $request->secretary_email,
            'phone' => $request->secretary_phone,
            'password' => bcrypt($request->secretary_password),
            'position' => 'Secretary',
            'unioun' => $uniouninfo->id,
            'role' => 'Secretary', // Assuming you have a role field
        ]);

        $origin_url =  "https://$request->short_name_e.uniontax.gov.bd";
        $allowedAccess = AllowedOrigin::create(['origin_url'=>$origin_url]);

        $origin_url =  "https://$request->short_name_e.unionservices.gov.bd";
        $allowedAccess = AllowedOrigin::create(['origin_url'=>$origin_url]);


        // Handle file uploads if necessary
        if ($request->hasFile('web_logo')) {
            $uniouninfo->saveFile($request->file('web_logo'), 'web_logo');
        }
        if ($request->hasFile('sonod_logo')) {
            $uniouninfo->saveFile($request->file('sonod_logo'), 'sonod_logo');
        }
        if ($request->hasFile('c_signture')) {
            $uniouninfo->saveFile($request->file('c_signture'), 'c_signture');
        }
        if ($request->hasFile('socib_signture')) {
            $uniouninfo->saveFile($request->file('socib_signture'), 'socib_signture');
        }
        if ($request->hasFile('u_image')) {
            $uniouninfo->saveFile($request->file('u_image'), 'u_image');
        }

        // Return a success response
        return response()->json([
            'message' => 'Union and users created successfully',
            'uniouninfo' => $uniouninfo,
            'chairman' => $chairman,
            'secretary' => $secretary,
        ], 201);
    }

}
