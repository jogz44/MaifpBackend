<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class lab_examination_details extends Model
{
    //

    protected $table = 'lab_examination_details';


    protected $fillable =  [
        'transaction_id',
        'new_consultation_id',
            'item_id',
            'item_description',
            'service_fee',
            'total_amount',
            'selling_price'

    ];
    protected $casts = [
        'selling_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

}
