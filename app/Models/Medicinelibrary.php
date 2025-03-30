<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicinelibrary extends Model
{
    //
    use HasFactory;
    protected $table = 'tbl_medicinelibrary';

    protected $fillable =[
        'brand_name',
        'generic_name',
        'dosage_form',
        'dosage',
        'category',
        'user_id',
    ];
}
