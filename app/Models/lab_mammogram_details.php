<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class lab_mammogram_details extends Model
{
    //

    protected $table = 'lab_mammogram_details';

    protected $fillable =[

        'transaction_id',
        'new_consultation_id',
        'procedure',
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
