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
        'service_fee',
        'total_amount'

    ];

    // protected $casts = [
    //     'selling_price' => 'decimal:2',
    //     'total_amount' => 'decimal:2',
    // ];

}
