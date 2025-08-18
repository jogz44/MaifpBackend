<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class budget_additional_funds extends Model
{
    //

    protected $table = 'budget_additional_funds';


    protected $fillable = [

        'budget_id',
        'additional'
    ];
}
