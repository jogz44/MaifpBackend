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
        'laboratory_total',
        'medication_total',
        'total_billing',
        'discount',
        'final_billing',
        'fund_source',
        'fund_amount',
    ];



    public function funds()
    {
        return $this->hasMany(AssistancesFunds::class, 'assistance_id');
    }
}
