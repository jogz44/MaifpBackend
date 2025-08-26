<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    //

    protected $table = 'medication_details';

    protected $fillable =[

        'medication_id',
        'item_description',
        'patient_id', // the costumeer is the patient_id
        'quantity',
        'unit',
        'transaction_date',
        'amount',
        // 'status',
        'user_id',


    ];

     public function consultation()
    {
        return $this->belongsTo(New_Consultation::class, 'new_consultation_id');
    }
}
