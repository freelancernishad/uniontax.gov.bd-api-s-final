<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'c_type_en',
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
        'chairman_phone',  // New column
        'secretary_phone', // New column
        'udc_phone',       // New column
        'user_phone',      // New column
    ];

    protected $appends = ['is_popup','has_bank_account'];

    /**
     * Get the value of is_popup.
     *
     * @return bool
     */
    public function getIsPopupAttribute()
    {
        return empty($this->chairman_phone) ||
               empty($this->secretary_phone) ||
               empty($this->udc_phone) ||
               empty($this->user_phone);
    }

    public function getHasBankAccountAttribute()
    {
        // Assuming you have a BankAccount model and a `union` column in it
        return BankAccount::where('union', $this->short_name_e)->exists();
    }
        /**
     * Save a file to S3 and update the model attribute with the file path.
     *
     * @param $file
     * @param string $attribute
     * @return string
     */
    public function saveFile($file, $attribute, $dir = '')
    {
        // Define the directory
        $directory = $dir ? 'uniouninfo/' . $dir : 'uniouninfo';

        if ($file) {
            // Generate a unique file name
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Store the file in the protected disk
            $filePath = Storage::disk('protected')->putFileAs($directory, $file, $fileName);

            // Save the file path to the model attribute
            $this->$attribute = $filePath;
            $this->save();

            return $filePath;
        }

        return null;
    }


    public function villages()
    {
        return $this->hasMany(Village::class, 'unioninfo_id');
    }

    public function postOffices()
    {
        return $this->hasMany(PostOffice::class, 'unioninfo_id');
    }


}
