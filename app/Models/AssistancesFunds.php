<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssistancesFunds extends Model
{
    //
    protected $fillable = [
        'assistance_id',
        'fund_source',
        'fund_amount',
    ];

    protected $casts =[
        'fund_amount' => 'decimal:2',
    ];

    public function assistance()
    {
        return $this->belongsTo(Assistances::class, 'assistance_id');
    }
}
