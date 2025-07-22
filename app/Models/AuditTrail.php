<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class AuditTrail extends Model
{
    //
      use HasFactory;

     protected $table ='tbl_auditlogs';

    protected $fillable = [
       "action",
        "table_name",
        "user_id",
        "changes",
    ];
}
