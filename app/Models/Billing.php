<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    //

    protected $table ='billing';

    protected $fillable = [

        'patient_id',
        'laboratory_id',
        'laboratory_type',
        'amount',
        'status'

    ];
}
