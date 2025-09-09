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

    // protected static function booted()
    // {
    //     static::saved(function ($medication) {
    //         if ($medication->status === 'Done') {
    //             $transaction = Transaction::with('consultation')
    //                 ->find($medication->transaction_id);

    //             if ($transaction && $transaction->consultation) {
    //                 $consultation = $transaction->consultation;

    //                 // ✅ Update status and add 500 to amount
    //                 $consultation->update([
    //                     'status' => 'Done',
    //                     'amount' => ($consultation->amount ?? 0) + 500
    //                 ]);
    //             }
    //         }
    //     });
    // }

    // protected static function booted()
    // {
    //     static::saved(function ($medication) {
    //         if ($medication->status === 'Done') {
    //             $transaction = Transaction::with('consultation')
    //                 ->find($medication->transaction_id);

    //             if ($transaction && $transaction->consultation) {
    //                 $consultation = $transaction->consultation;

    //                 // Fetch doctor amount (adjust if you have doctor_id reference)
    //                 $doctor = lib_doctor::first(); // or lib_doctor::find($consultation->doctor_id)

    //                 if ($doctor) {
    //                     $consultation->update([
    //                         'status' => 'Done',
    //                         'amount' => ($consultation->amount ?? 0) + $doctor->doctor_amount,
    //                     ]);
    //                 }
    //             }
    //         }
    //     });
    // }

    protected static function booted()
    {
        static::saved(function ($medication) {
            if ($medication->status === 'Done') {
                $transaction = Transaction::with(['consultation', 'laboratories'])
                    ->find($medication->transaction_id);

                if ($transaction) {
                    // ✅ If transaction has a consultation, update it
                    if ($transaction->consultation) {
                        $consultation = $transaction->consultation;

                        // Fetch doctor amount (adjust if you have doctor_id reference)
                        $doctor = lib_doctor::first(); // or lib_doctor::find($consultation->doctor_id)

                        if ($doctor) {
                            $consultation->update([
                                'status' => 'Done',
                                'amount' => ($consultation->amount ?? 0) + $doctor->doctor_amount,
                            ]);
                        }
                    }

                    // ✅ If transaction has laboratories, update them too
                    if ($transaction->laboratories && $transaction->laboratories->count() > 0) {
                        foreach ($transaction->laboratories as $laboratory) {
                            $laboratory->update([
                                'status' => 'Done',
                            ]);
                        }
                    }
                }
            }
        });
    }
}
