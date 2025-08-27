<?php

namespace App\Models;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    //

    protected $table = 'medication';

    protected $fillable = [
         'status',
         'transaction_id'
    ];

    // app/Models/Medication.php

    protected static function booted()
    {
        static::saved(function ($medication) {
            if ($medication->status === 'Done') {
                $transaction = Transaction::with('consultation')
                    ->find($medication->transaction_id);

                if ($transaction && $transaction->consultation) {
                    $consultation = $transaction->consultation;

                    // ✅ Update status and add 500 to amount
                    $consultation->update([
                        'status' => 'Done',
                        'amount' => ($consultation->amount ?? 0) + 500
                    ]);
                }
            }
        });
    }
}
