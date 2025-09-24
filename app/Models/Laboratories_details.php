<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratories_details extends Model
{
    //

    protected $table = 'laboratory_details'; // specify the table name if it doesn't follow Laravel's naming convention

    protected $fillable = [
        'transaction_id',
        'new_consultation_id',
        'laboratory_type',
        'amount',
        'service_fee',
        'total_amount'
        // 'status',
    ];

    public function consultation()
    {
        return $this->belongsTo(New_Consultation::class, 'new_consultation_id');
    }
}
