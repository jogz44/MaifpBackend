<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class New_Consultation extends Model
{
    //


    protected $table = 'new_consultation';

    protected $fillable =[
        'patient_id',
        'transaction_id',
        'consultation_time',
        'consultation_date',
        'status',
    ];
}
