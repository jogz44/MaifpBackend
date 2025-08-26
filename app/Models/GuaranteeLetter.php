<?php

namespace App\Models;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;

class GuaranteeLetter extends Model
{
    //

    protected $table = 'guarantees';

    protected $fillable = [
        'patient_id',
        'transaction_id',
        'consultation_amount',
        'laboratory_total',
        'medication_total',
        'total_billing',
        'status'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

}
