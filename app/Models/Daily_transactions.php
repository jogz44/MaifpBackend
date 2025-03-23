<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class daily_transactions extends Model
{
    //
    use HasFactory;
    protected $table ='tbl_daily_transactions';

    protected $fillable = [
        'item_id',
        'transaction_id',
        'customer_id',
        'quantity',
        'transaction_date',
        'user_id',
    ];
}
