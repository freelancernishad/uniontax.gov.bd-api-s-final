<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeLicenseKhat extends Model
{
    use HasFactory;

    protected $fillable = [
        'khat_id',
        'name',
        'main_khat_id',
        'type',
    ];
}
