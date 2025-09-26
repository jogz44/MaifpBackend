<?php

namespace App\Models;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;

class GuaranteeLetter extends Model
{
    //

    protected $table = 'guarantee_letter';

    protected $fillable = [
        'patient_id',
        'transaction_id',
        'consultation_amount',
        'laboratory_total',
        'medication_total',
        'total_billing',
        'discount',
        'final_billing',
        'laboratories_details',
        'medication'

        // 'status'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function funds()
    {
        return $this->hasMany(AssistancesFunds::class, 'assistance_id');
    }


}
