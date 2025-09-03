<?php

namespace App\Models;

use App\Models\Patient;
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
        'amount',
        'status',
    ];

    public function laboratories()
    {
        return $this->hasMany(Laboratory::class, 'new_consultation_id');
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
