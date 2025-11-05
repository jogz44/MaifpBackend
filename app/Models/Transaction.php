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
        'representative_id',
        'maifip',
        'philhealth',
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

    public function ultrasound_details()
    {

        return $this->hasMany(lab_ultrasound_details::class, 'transaction_id');
    }

    public function mammogram_details()
    {

        return $this->hasMany(lab_mammogram_details::class, 'transaction_id');
    }


    public function radiologies_details()
    {

        return $this->hasMany(lab_radiology_details::class, 'transaction_id');
    }


    public function examination_details()
    {

        return $this->hasMany(lab_examination_details::class, 'transaction_id');
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

    public function funds()
    {
        return $this->hasMany(AssistancesFunds::class, 'assistance_id');
    }

    public function assistances()
    {
        return $this->hasMany(Assistances::class, 'transaction_id');
    }

}
