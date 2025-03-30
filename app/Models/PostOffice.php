<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostOffice extends Model
{
    use HasFactory;

    protected $fillable = ['unioninfo_id', 'name_bn', 'name_en', 'post_code'];

    public function unioninfo()
    {
        return $this->belongsTo(Uniouninfo::class, 'unioninfo_id');
    }
}
