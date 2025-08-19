<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    //

    protected $table ='laboratory';

    protected $fillable = [
        'transaction_id',
        'new_consultation_id',
        'laboratory_type',
        'amount',
        'status'

    ];

    public function consultation()
    {
        return $this->belongsTo(New_Consultation::class, 'new_consultation_id');
    }
}
