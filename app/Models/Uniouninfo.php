<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uniouninfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'short_name_e',
        'domain',
        'portal',
        'short_name_b',
        'thana',
        'district',
        'web_logo',
        'sonod_logo',
        'c_signture',
        'c_name',
        'c_type',
        'c_email',
        'socib_name',
        'socib_signture',
        'socib_email',
        'format',
        'u_image',
        'u_description',
        'u_notice',
        'u_code',
        'contact_email',
        'google_map',
        'defaultColor',
        'payment_type',
        'AKPAY_MER_REG_ID',
        'AKPAY_MER_PASS_KEY',
        'smsBalance',
        'nidServicestatus',
        'nidService',
        'status',
        'type',
        'full_name_en',
        'c_name_en',
        'district_en',
        'thana_en',
        'socib_name_en',
    ];


        /**
     * Save a file to S3 and update the model attribute with the file path.
     *
     * @param $file
     * @param string $attribute
     * @return string
     */
    public function saveFile($file, $attribute,$dir='')
    {


        if($dir){
            $directory = 'uniouninfo/'.$dir;
        }else{
            $directory = 'uniouninfo';
        }

        if ($file) {
            $filePath = uploadFileToS3($file, $directory); // Upload to S3
            $this->$attribute = $filePath;
            $this->save();

            return $filePath;
        }

        return null;
    }
}
