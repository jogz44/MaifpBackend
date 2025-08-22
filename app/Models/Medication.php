<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    //

    protected $table = 'medication';

    protected $fillable =[

        'transaction_id',
        'new_consultation_id',
        'item_id',
        'quantity',
        'capsule',
    ];
}
