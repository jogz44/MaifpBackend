<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

     public function user(){
         return $this->belongsTo(User::class, 'userid');
     }
}
