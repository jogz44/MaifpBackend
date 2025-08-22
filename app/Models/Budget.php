<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    //

    protected $table = 'budget';

    protected $fillable = [
        // 'budget_start_date',
        // 'budget_end_date',
        'funds',
        // 'remaining_funds',
        'remarks'

    ];


    public function releases(){

        return $this->hasMany(BudgetRelease::class);
    }

    public function releaseFunds($amount)
    {

        if ($this->remaining_funds < $amount) {
            throw new Exception('Insufficient funds');
        }
        // Deduct from remaining funds
        $this->remaining_funds -= $amount;
        $this->save();

        // Log the release
        $this->releases()->create([
            'release_amount' => $amount,
        ]);

    }
}
