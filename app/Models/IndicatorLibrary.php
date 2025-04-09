<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IndicatorLibrary extends Model
{
    //
    use HasFactory;

    protected $table ='tbl_libIndicator';

    protected $fillable =[
        'transaction_date',
        'is_open',
        'is_close'
    ];
}
