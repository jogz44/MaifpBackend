<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    //

    public function billing($transactionId)
    {
        $transaction = Transaction::with([
            'patient:id,firstname,lastname',
            'consultation:id,transaction_id,amount',
            'laboratories:id,transaction_id,laboratory_type,amount,status',
        ])->findOrFail($transactionId);

        $consultationAmount = $transaction->consultation?->amount ?? 0;
        $laboratoryTotal = $transaction->laboratories->sum('amount');
        $totalBilling = $consultationAmount + $laboratoryTotal;

        return response()->json([
            'transaction_id'      => $transaction->id,
            'firstname'           => $transaction->patient->firstname,
            'lastname'            => $transaction->patient->lastname,
            'transaction_type'    => $transaction->transaction_type,  // ✅ Directly here
            'transaction_date'    => $transaction->transaction_date,  // ✅ Directly here
            'consultation_amount' => $consultationAmount,
            'laboratory_total'    => $laboratoryTotal,
            'total_billing'       => $totalBilling,
        ]);
    }
}
