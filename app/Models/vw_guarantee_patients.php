<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vw_guarantee_patients extends Model
{
    //

    protected $table = 'vw_guarantee_patients';

    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'patient_id', 'patient_id')
            ->where('status', 'Complete')
            ->whereDoesntHave('guaranteeLetter', function ($q) {
                $q->where('status', 'Funded');
            });
    }
}
