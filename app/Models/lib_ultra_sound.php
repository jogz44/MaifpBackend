<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class lib_ultra_sound extends Model
{
    //

    protected $table = 'lib_ultra_sound';


    protected $fillable = [

            'body_parts',
            'rate',
            'service_fee',
            'total_amount'
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];
}
