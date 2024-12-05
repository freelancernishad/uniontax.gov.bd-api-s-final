<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sonodnamelist extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'bnname',
        'enname',
        'icon',
        'template',
        'sonod_fee'
    ];




    // Relationship with SonodFee (one-to-many)
    public function sonodFees()
    {
        return $this->hasMany(SonodFee::class, 'service_id', 'service_id');
    }



    public function saveIcon($file)
    {
        $filePath = uploadFileToS3($file, 'sonod/icon'); // Define the S3 directory
        $this->icon = $filePath;
        $this->save();

        return $filePath;
    }

}
