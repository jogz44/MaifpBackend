<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class lab_ultrasound_details extends Model
{
    //

    protected $table = 'lab_ultrasound_details';

    protected $fillable = [

        'transaction_id',
        'new_consultation_id',
        'body_parts',
        'rate',
        'service_fee',
        'total_amount',
    ];

    protected $casts = [

        'rate' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

}
