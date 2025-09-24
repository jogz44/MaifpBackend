<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class lib_radiology extends Model
{
    //

    protected $table = 'lib_radiology';

    protected $fillable = [

        'item_description',
        'service_fee',
        'amount'
    ];
}
