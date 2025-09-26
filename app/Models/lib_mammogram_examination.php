<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class lib_mammogram_examination extends Model
{
    //

    protected $table = 'lib_mammogram_examination';



    protected $fillable =[

        'procedure',
        'rate',
        'service_fee',
        'total_amount'

    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];
}
