<?php

namespace App\Models;

use App\Models\vital;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //

    protected $table = 'transaction';

    protected $fillable = [
        'transaction_number',
        'patient_id',
        'transaction_type',
        'transaction_mode',
        'transaction_date',
        'purpose'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class,);
    }

    public function vital()
    {
        return $this->hasOne(vital::class);
    }
}
