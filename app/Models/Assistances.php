<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assistances extends Model
{
    //
    protected $table = 'assistances';

    protected $fillable = [
        'patient_id',
        'transaction_id',
        'consultation_amount',
        // 'laboratory_total',
        'radiology_total',
        'examination_total',
        'ultrasound_total',
        'mammogram_total',

        'medication_total',
        'total_billing',
        'discount',
        'final_billing',
        'fund_source',
        'fund_amount',
        'status',
        // 'laboratories_details',
        'ultrasound_details',
        'mammogram_details',
        'examination_details',
        'radiology_details',

        'medication',
        'gl_number'
    ];

    protected $casts = [
        'medication' => 'array',
        'laboratories_details' => 'array',
    ];

    public function funds()
    {
        return $this->hasMany(AssistancesFunds::class, 'assistance_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function medications()
    {
        return $this->hasMany(Medication::class, );
    }

    public function laboratories_details()
    {
        return $this->hasMany(laboratories_Details::class, );
    }
}
