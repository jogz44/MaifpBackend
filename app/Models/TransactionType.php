<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    //

    protected $table = 'transaction_type';

    protected $fillable = [
        'transaction_name',

    ];

    protected $attributes = [
        'status' => true, // default when not provided
    ];

    // ✅ Accessor for readable status
    protected $appends = ['status_label'];

    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Available' : 'Not Available';
    }
}
