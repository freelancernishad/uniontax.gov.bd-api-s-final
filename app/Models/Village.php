<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $fillable = ['unioninfo_id', 'name_bn', 'name_en', 'word_no'];

    public function unioninfo()
    {
        return $this->belongsTo(Uniouninfo::class, 'unioninfo_id');
    }
}
