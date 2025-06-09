<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Configurations extends Model
{
    //
    use HasFactory;

     protected $table ='lib_sysconf';

    protected $fillable = [
       "normal_color",
        "low_color",
        "empty_color",
        "low_count",
        "days_toExpire"
    ];

}
