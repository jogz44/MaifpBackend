<?php

namespace App\Models;

use App\Models\vital;
use App\Models\Patient;
use App\Models\GuaranteeLetter;
use App\Models\New_Consultation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    //
    use HasFactory;

    protected $table = 'transaction';

    protected $fillable = [
        'transaction_number',
        'patient_id',
        'transaction_type',
        'transaction_mode',
        'transaction_date',
        'purpose',
        'status', // Added status field
    ];


    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function vital()
    {
        return $this->hasOne(vital::class);
    }
    public function consultation()
    {
        return $this->hasOne(New_Consultation::class);
    }

    // public function getTotalBillingAttribute()
    // {
    //     $consultationAmount = $this->consultation ? $this->consultation->amount : 0;
    //     $laboratoryTotal = $this->laboratories()->sum('amount');

    //     return $consultationAmount + $laboratoryTotal;
    // }
    public function laboratories()
    {
        return $this->hasMany(Laboratory::class, 'transaction_id');
    }
    public function guaranteeLetter()
    {
        return $this->hasOne(GuaranteeLetter::class, 'transaction_id');
    }
}
