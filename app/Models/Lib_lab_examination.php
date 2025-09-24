<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lib_lab_examination extends Model
{
    //

    protected $table = 'lib_laboratory_examination';

    protected $fillable = [

        'item_id',
        'item_description',
        'service_fee',
        'amount'
    ];
}
