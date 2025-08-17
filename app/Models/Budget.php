<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    //

    protected $table = 'budget';

    protected $fillable = [
        'budget_date',
        'funds',
        'additional_funds',
        'remaining_funds',
        'release_funds',
    ];
}
