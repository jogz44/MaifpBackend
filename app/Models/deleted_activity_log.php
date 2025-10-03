<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class deleted_activity_log extends Model
{
    //

    protected $table = 'deleted_activity_log';
    public $timestamps = false; // 👈 prevent Laravel from inserting updated_at

    protected $fillable = [
        'log_name',
        'description',
        'subject', 'subject',
        'causer', 'causer',
        'properties',
        'log_name',
        'created_at',
        'deleted_at',
    ];
}
