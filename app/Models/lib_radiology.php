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
        'total_amount',
        'selling_price'
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'service_fee' => 'decimal:2',
    ];
}
