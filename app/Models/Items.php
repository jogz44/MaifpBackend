<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class items extends Model
{
    //
    use HasFactory;

    protected $table ='tbl_items';

    protected $fillable =[
        'po_no',
        'brand_name',
        'generic_name',
        'dosage_form',
        'dosage',
        'category',
        'unit',
        'quantity',
        'price',
        'expiration_date',
        'user_id',
    ];
}
