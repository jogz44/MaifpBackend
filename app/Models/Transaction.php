<?php

namespace App\Models;

use App\Models\vital;
use App\Models\Patient;
use App\Models\Medication;
use App\Models\Representative;
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
        'representative_id'
    ];


    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function vital()
    {
        return $this->hasOne(vital::class);
    }
    public function consultation()
    {
        return $this->hasOne(New_Consultation::class);
    }

    public function laboratories()
    {
        return $this->hasMany(Laboratory::class, 'transaction_id');
    }

    public function laboratories_details()
    {

        return $this->hasMany(Laboratories_details::class, 'transaction_id');
    }


    public function guaranteeLetter()
    {
        return $this->hasOne(GuaranteeLetter::class, 'transaction_id');
    }

    public function medication()
    {
        return $this->hasOne(Medication::class, 'transaction_id');
    }

    public function medicationDetails()
    {
        return $this->hasMany(Medication_details::class, 'transaction_id');
    }

    public function assistance()
    {
        return $this->hasOne(Assistances::class, 'transaction_id');
    }

}
