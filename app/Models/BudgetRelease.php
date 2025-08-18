<?php

namespace App\Models;

use App\Models\Budget;
use Illuminate\Database\Eloquent\Model;

class BudgetRelease extends Model
{
    //
    protected $table = 'budget_releases_funds';

    protected $fillable = [
            'budget_id',
            'release_amount',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }
}
