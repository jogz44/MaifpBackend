<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class system_users extends Model
{
    //
    use HasFactory;

    protected $table = 'tbl_system_users';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'position',
        'role',
        'user_name',
        'password',
        'user_id'
    ];


    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed'
    ];
}
