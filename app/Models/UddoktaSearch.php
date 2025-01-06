<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UddoktaSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'sonod_name',
        'nid_number',
        'uddokta_id',
        'api_response',
    ];
}
