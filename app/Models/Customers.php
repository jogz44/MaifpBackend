<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customers extends Model
{
    //
    use HasFactory;

    protected $table ='tbl_customers';

    protected $fillable = [
        'firstname',
        'lastname',
        'middlename',
        'ext',
        'birthdate',
        'contact_number',
        'age',
        'gender',
        'is_not_tagum',
        'street',
        'purok',
        'barangay',
        'city',
        'province',
        'category',
        'is_pwd',
        'is_solo',
        'user_id'
    ];
}
