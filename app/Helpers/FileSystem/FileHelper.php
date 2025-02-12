<?php

use Aws\S3\S3Client;
use Illuminate\Support\Str;
use Intervention\Image\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;




/**
     * Upload a file to the S3 disk.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string
     * @throws \Exception
     */
    function uploadFileToS3($file, $directory = 'uploads')
    {
        // Validate the file
        if (!$file->isValid()) {
            \Log::error('Invalid file upload');
            throw new \Exception('Invalid file upload');
        }

        // Generate a unique file name
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Try storing the file in the 's3' disk under the specified directory
        try {
            $filePath = $file->storeAs($directory, $fileName, 's3');

            if ($filePath === false) {
                \Log::error('S3 file upload failed');
                throw new \Exception('Failed to upload file to S3');
            }

            \Log::info('File uploaded to S3', ['file_path' => $filePath]);

            // Return the file path
            return config('AWS_FILE_LOAD_BASE').$filePath;
        } catch (\Exception $e) {
            \Log::error('Error uploading file to S3: ' . $e->getMessage());
            throw $e;
        }
    }


    function uploadDocumentsToS3($fileData, $filePath, $dateFolder, $sonodId)
{
    if (!$fileData) {
        Log::error('No file data provided.');
        return null;
    }

    // Handle case where fileData is inside an array
    if (is_array($fileData) && isset($fileData[0])) {
        $fileData = $fileData[0];
    }

    // Define the directory structure
    $directory = "sonod/$filePath/$dateFolder/$sonodId";
    $fileName = time() . '_' . Str::random(10);

    // Check if it's a base64-encoded string
    if (is_string($fileData) && preg_match('/^data:image\/(\w+);base64,/', $fileData, $matches)) {
        $base64Data = substr($fileData, strpos($fileData, ',') + 1);
        $decodedData = base64_decode($base64Data);
        $extension = $matches[1];

        $fileName .= '.' . $extension;
        $filePath = "$directory/$fileName";

        // Upload to S3
        Storage::disk('s3')->put($filePath, $decodedData);
    }
    // Handle regular file uploads
    elseif ($fileData instanceof UploadedFile) {
        $fileName .= '.' . $fileData->getClientOriginalExtension();
        $filePath = Storage::disk('s3')->putFileAs($directory, $fileData, $fileName);
    }
    // Invalid file type
    else {
        Log::error('Invalid file upload', ['fileData' => $fileData, 'type' => gettype($fileData)]);
        throw new \Exception('Invalid file upload');
    }

    Log::info('File uploaded to S3', ['file_path' => $filePath]);

    return $filePath;
}



function getUploadDocumentsToS3($filename)
{
    if (!$filename) {
        return null; // If filename is empty, return null instead of an error
    }

    try {
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $bucket = env('AWS_BUCKET');

        $cmd = $s3->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key'    => $filename,
        ]);

        $request = $s3->createPresignedRequest($cmd, '+5 minutes');
        return (string) $request->getUri(); // Return only the presigned URL
    } catch (\Exception $e) {
        Log::error('Error generating S3 presigned URL: ' . $e->getMessage());
        return null;
    }
}



/**
 * Upload a file to the 'protected' disk.
 *
 * @param \Illuminate\Http\UploadedFile $file
 * @param string $directory
 * @return string $filePath
 */
function uploadFileToProtected($file, $directory = 'uploads')
{
    // Validate file
    if (!$file->isValid()) {
        throw new \Exception('Invalid file upload');
    }

    // Store file in the 'protected' disk
    $filePath = $file->store($directory, 'protected');

    return $filePath;
}

/**
 * Read a file from the 'protected' disk.
 *
 * @param string $filename
 * @return \Symfony\Component\HttpFoundation\StreamedResponse
 */
function readFileFromProtected($filename)
{
    // Define file path
    $filePath = "uploads/{$filename}";

    // Check if the file exists
    if (!Storage::disk('protected')->exists($filePath)) {
        throw new \Exception('File not found');
    }

    // Return file as download
    return Storage::disk('protected')->download($filePath);
}




function fileupload($Image,$path,$width='',$height='',$customname='')
{
 // same file server
 if (!file_exists(env('FILE_PATH').$path)) {
    File::makeDirectory(env('FILE_PATH').$path, 0777, true, true);
}

 $position = strpos($Image, ';');
$sub = substr($Image, 0, $position);
$ext = explode('/', $sub)[1];
$random = rand(10000,99999);
if($customname!=''){
$name = time().'____'.$customname.'.'.$ext;
}else{
$name = time().'____'.$random.'.'.$ext;
}
$upload_path = $path;
$image_url = $upload_path.$name;

if($width=='' && $height==''){

    $img = Image::make($Image);
}else{

    $img = Image::make($Image)->resize($width, $height);
}



 $img->save(env('FILE_PATH').$image_url);
 return $image_url;

    // separate file server
// $url = env('FILE_SERVER');
// $curl = curl_init($url);
// curl_setopt($curl, CURLOPT_URL, $url);
// curl_setopt($curl, CURLOPT_POST, true);
// curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// // $headers = array(
// //    "Content-Type: application/json",
// // );
// // curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
// $data = ["files"=> $Image,'customname'=>$customname,'path'=>$path,'width'=>$width,'height'=>$height];
// curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
// $resp = curl_exec($curl);
// curl_close($curl);
// return json_decode($resp);

}






function base642($Image)
{
    $url = $Image;
    $image = file_get_contents($url);
    if ($image !== false){
        return 'data:image/jpg;base64,'.base64_encode($image);

    }
}



function base64($Image)
{
//  return $Image;

    if(File::exists(env('FILE_PATH').$Image)){

        $Image= env('FILE_PATH').$Image;
    }else{
        $Image= env('FILE_PATH').'backend/image.png';

    }

$ext =  pathinfo($Image, PATHINFO_EXTENSION);;
    return $b64image = "data:image/$ext;base64,".base64_encode(file_get_contents($Image));
}






function handleFileUrl($filePath, $defaultImage = 'https://api.uniontax.gov.bd/backend/image.png')
{
    if (!$filePath) {
        return $defaultImage;
    }

    try {
        if (!isLocalRequest()) {
            return url("/files/{$filePath}");
        } else {
            return $defaultImage;
        }
    } catch (\Exception $e) {
        return $defaultImage;
    }
}


function isLocalRequest()
{
    $host = Request::getHost();
    $port = Request::getPort(); // Get the port number
    return in_array($host, ['localhost', '127.0.0.1']) && $port === 8000; // Adjust the port as needed
}



function handleFileUploads($request, &$insertData, $filePath, $dateFolder, $sonodId)
{
    // Handle file uploads to S3
    if (isset($request->bn['image']) && $request->bn['image']) {
        uploadFile($request->bn['image'], $insertData, 'image', $filePath, $dateFolder, $sonodId);
    }

    if ($request->hasFile('applicant_national_id_front_attachment')) {
        $insertData['applicant_national_id_front_attachment'] = uploadDocumentsToS3(
            $request->file('applicant_national_id_front_attachment'),
            $filePath,
            $dateFolder,
            $sonodId
        );
    }

    if ($request->hasFile('applicant_national_id_back_attachment')) {
        $insertData['applicant_national_id_back_attachment'] = uploadDocumentsToS3(
            $request->file('applicant_national_id_back_attachment'),
            $filePath,
            $dateFolder,
            $sonodId
        );
    }

    if ($request->hasFile('applicant_birth_certificate_attachment')) {
        $insertData['applicant_birth_certificate_attachment'] = uploadDocumentsToS3(
            $request->file('applicant_birth_certificate_attachment'),
            $filePath,
            $dateFolder,
            $sonodId
        );
    }
}



function uploadFile($fileData, &$insertData, $field, $filePath, $dateFolder, $sonodId)
{
    if ($fileData) {
        // Define the directory for the file
        $directory = "sonod/$filePath/$dateFolder/$sonodId";

        // Generate a unique file name
        $fileName = time() . '_' . Str::random(10);

        // Check if the input is base64 data
        if (preg_match('/^data:image\/(\w+);base64,/', $fileData, $matches)) {
            // Extract the base64 data
            $base64Data = substr($fileData, strpos($fileData, ',') + 1);

            // Decode the base64 data
            $decodedData = base64_decode($base64Data);

            // Determine the file extension from the MIME type
            $extension = $matches[1]; // e.g., 'png', 'jpeg'

            // Generate the full file name with extension
            $fileName .= '.' . $extension;

            // Store the file in the protected disk
            $filePath = Storage::disk('protected')->put("$directory/$fileName", $decodedData);


        } else {
            // Handle file object (e.g., uploaded file)
            $fileName .= '.' . $fileData->getClientOriginalExtension();

            // Store the file in the protected disk
            $filePath = Storage::disk('protected')->putFileAs($directory, $fileData, $fileName);
        }

        // Log::info("$directory/$fileName");

        // Save the file path in the insertData array
        $insertData[$field] = "$directory/$fileName";
    }
}
