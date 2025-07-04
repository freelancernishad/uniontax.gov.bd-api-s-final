<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobStatusLog extends Model
{
    protected $fillable = ['job_name', 'status', 'message'];
}
