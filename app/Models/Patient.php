<?php

namespace App\Models;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    //
    use HasFactory;

    protected $table = 'patient';

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

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

}
