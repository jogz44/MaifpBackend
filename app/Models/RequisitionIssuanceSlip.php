<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionIssuanceSlip extends Model
{
    //
    use HasFactory;
    protected $table = 'tbl_ris';

    protected $fillable = [
        'transaction_date',
        'purpose',
        'ris_id',
        'userid'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function scopeDateBetween($query, $from, $to)
    {
        return $query->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
    }
}
