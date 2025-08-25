<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    //

    protected $table = 'budget';

    protected $fillable = [

        'funds',

        'remarks'

    ];




}
