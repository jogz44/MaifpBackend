<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Representative extends Model
{
    //
    use HasFactory;

    protected $table = 'representatives';

    protected $fillable = [
       'rep_name',
        'rep_relationship',
        'rep_contact',
        'rep_barangay',
        'rep_address',
        'rep_purok',
        'rep_street',
        'rep_city',
        'rep_province'
    ];

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }
}
