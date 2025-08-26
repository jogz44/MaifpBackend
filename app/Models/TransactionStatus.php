<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionStatus extends Model
{
    //

    protected $table = 'medication';

    protected $fillable = [
         'status',
         'transaction_id'
    ];
}
