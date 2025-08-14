<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vital extends Model
{
    //

    protected $table = 'vital';

    protected $fillable = [
        'transaction_id',
        'patient_id',
        'height',
        'weight',
        'bmi',
        'waist',
        'pulse_rate',
        'temperature',
        'sp02',
        'heart_rate',
        'blood_pressure',
        'respiratory_rate',
        'medicine',
        'LMP',
        // 'created_at',
        // 'updated_at'
    ];
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
