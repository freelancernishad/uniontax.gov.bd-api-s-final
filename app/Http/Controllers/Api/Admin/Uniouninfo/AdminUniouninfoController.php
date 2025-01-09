<?php
namespace App\Http\Controllers\Api\Admin\Uniouninfo;

use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
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
}
