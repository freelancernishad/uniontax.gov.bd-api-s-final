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
}
