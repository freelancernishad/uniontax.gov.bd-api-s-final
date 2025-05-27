<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SonodFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'sonod_id',
        'type',
        'file_path',
    ];

    public function sonod()
    {
        return $this->belongsTo(Sonod::class);
    }


      /**
     * Wrapper to upload and save document using global upload function.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $type
     * @param string $filePath
     * @param string $dateFolder
     * @param int $sonodId
     * @return SonodFile|null
     */
    public static function uploadAndSave($file, $type, $filePath, $dateFolder, $sonodId)
    {
        $url = uploadDocumentsToS3($file, $filePath, $dateFolder, $sonodId);

        if ($url) {
            return self::create([
                'sonod_id' => $sonodId,
                'type'     => $type,
                'file_path' => $url,
            ]);
        }

        return null;
    }

}
