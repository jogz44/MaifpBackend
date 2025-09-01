<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class lib_laboratory extends Model
{
    //

    protected $table= 'lib_laboratory';


    protected $fillable =[
        'lab_name',
        'lab_amount',

    ];

}
