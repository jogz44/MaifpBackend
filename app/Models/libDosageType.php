<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class libDosageType extends Model
{
    //
      use HasFactory;

    protected $table ='lib_DosageType';

    protected $fillable =['type' ];

}
