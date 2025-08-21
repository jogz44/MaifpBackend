<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    //
    // this method will get the billing of the per transaction of the patient
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



    // this method is for fetching the patient on the billing
    public function index()
    {


        $transactions = Transaction::whereHas('patient')
            // ->whereDate('transaction_date', Carbon::today()) // ✅ only today's transactions
            // make sure patient exists
            ->where(function ($query) {
                // Case 1: Transaction with consultation
                $query->whereHas('consultation', function ($q) {
                    $q->where('status', 'Done');
                })
                    // Case 2: Transaction without consultation but with lab Done
                    ->orWhere(function ($q) {
                        $q->whereDoesntHave('consultation') // no consultation
                            ->whereHas('laboratories', function ($lab) {
                                $lab->where('status', 'Done');
                            });
                    });
            })
            ->with([
                'patient:id,firstname,lastname,middlename,ext,birthdate,age,contact_number,barangay',
                'consultation.laboratories', // load consultation + its labs
                // 'laboratories' // load labs directly tied to transaction
            ])
            ->get();


        return response()->json($transactions);
    }
}
