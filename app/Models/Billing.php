<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    //

    protected $table ='billing';

    protected $fillable = [
        'patient_id',
        'transaction_id',
        'consultation_total',
        'laboratory_total',
        'medication_total',
        'total_amount',
        'status'

    ];
}
