<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class role extends Model
{
     use HasFactory;

    protected $table = 'role';
    protected $fillable = ['role_name'];


    public function users()
    {
        return $this->hasMany(User::class);
    }
}
