<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCredentials extends Model
{
    //
    protected $table = 'tbl_UserCredentials';
    protected $fillable = [
        'userid',
        'module',
        'view',
        'add',
        'edit',
        'delete',
        'export'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userid');
    }
}
