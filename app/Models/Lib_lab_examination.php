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
        'total_amount',
        'selling_price'
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'service_fee' => 'decimal:2',
    ];
}
