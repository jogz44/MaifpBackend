<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Budget;
use App\Models\Billing;
use App\Models\Patient;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\BillingRequest;

class BillingController extends Controller
{
    //
    // this method will get the billing of the per transaction of the patient
    // this method will get the billing of the per transaction of the patient
    public function billing($transactionId)
    {
        $transaction = Transaction::with([
            'patient:id,firstname,lastname,age,gender,contact_number,street,purok,barangay',
            'consultation:id,transaction_id,amount',
            'laboratories:id,transaction_id,laboratory_type,amount,status',
        ])->findOrFail($transactionId);

        $consultationAmount = $transaction->consultation?->amount ?? 0;
        $laboratoryTotal = $transaction->laboratories->sum('amount');
        $totalBilling = $consultationAmount + $laboratoryTotal;

        return response()->json([
            'patient_id'      => $transaction->patient->id,
            'transaction_id'      => $transaction->id,
            'transaction_type'    => $transaction->transaction_type,
            'firstname'           => $transaction->patient->firstname,
            'lastname'            => $transaction->patient->lastname,
            'age'                 => $transaction->patient->age,
            'gender'              => $transaction->patient->gender,
            'contact_number'      => $transaction->patient->contact_number,
            'address'             => [
                'street'   => $transaction->patient->street,
                'purok'    => $transaction->patient->purok,
                'barangay' => $transaction->patient->barangay,
            ],
            'transaction_date'    => $transaction->transaction_date,
            'consultation_amount' => $consultationAmount,
            'laboratory_total'    => $laboratoryTotal,
            'total_billing'       => $totalBilling,
            'laboratories'        => $transaction->laboratories->map(function ($lab) {
                return [
                    'id'              => $lab->id,
                    'laboratory_type' => $lab->laboratory_type,
                    'amount'          => $lab->amount,
                    'status'          => $lab->status,
                ];
            }),
        ]);
    }

    public function index() //fetching the
    {
        $patients = Patient::whereHas('transaction', function ($query) {
            $query->whereDate('transaction_date', Carbon::today()) // ✅ only today's transactions
                ->where('status', '!=', 'Complete') // ✅ exclude completed transactions
                ->where(function ($q) {
                // ->whereDate('transaction_date', Carbon::today()) // ✅ only today's transactions
                // Case 1: Transaction with consultation Done
                $q->whereHas('consultation', function ($con) {
                    $con->where('status', 'Done');
                })
                    // Case 2: Transaction without consultation but with lab Done
                    ->orWhere(function ($q2) {
                        $q2->whereDoesntHave('consultation')
                            ->whereHas('laboratories', function ($lab) {
                                $lab->where('status', 'Done');
                            });
                    });
            });
        })
            ->with([
            'transaction' => function ($q) {
                $q->whereDate('transaction_date', Carbon::today()) // ✅ eager load only today's transaction
                 ->where('status', '!=', 'Complete'); // ✅ exclude completed here too
            }
        ])
            ->get([
                'id',
                'firstname',
                'lastname',
                'middlename',
                'ext',
                'birthdate',
                'age',
                'contact_number',
                'barangay'
            ]);

        return response()->json($patients);
    }
    
    // public function index()
    // {
    //     $patients = Patient::whereHas('transaction', function ($query) {
    //         $query->where('status', '!=', 'Complete') // ✅ exclude completed transactions
    //             ->where(function ($q) {
    //                 // Case 1: Transaction with consultation Done
    //                 $q->whereHas('consultation', function ($con) {
    //                     $con->where('status', 'Done');
    //                 })
    //                     // Case 2: Transaction without consultation but with lab Done
    //                     ->orWhere(function ($q2) {
    //                         $q2->whereDoesntHave('consultation')
    //                             ->whereHas('laboratories', function ($lab) {
    //                                 $lab->where('status', 'Done');
    //                             });
    //                     });
    //             });
    //     })
    //         ->with([
    //             'transaction' => function ($q) {
    //                 $q->where('status', '!=', 'Complete'); // ✅ exclude completed only
    //             }
    //         ])
    //         ->get([
    //             'id',
    //             'firstname',
    //             'lastname',
    //             'middlename',
    //             'ext',
    //             'birthdate',
    //             'age',
    //             'contact_number',
    //             'barangay'
    //         ]);

    //     return response()->json($patients);
    // }
    // public function index()
    // {
    //     $patients = Patient::whereHas('transaction', function ($query) {
    //         $query->where('status', '!=', 'Complete') // ✅ exclude completed transactions
    //             ->where(function ($q) {
    //                 // Case 1: Transaction with consultation Done
    //                 $q->whereHas('consultation', function ($con) {
    //                     $con->where('status', 'Done');
    //                 })
    //                     // Case 2: Transaction without consultation but with lab Done
    //                     ->orWhere(function ($q2) {
    //                         $q2->whereDoesntHave('consultation')
    //                             ->whereHas('laboratories', function ($lab) {
    //                                 $lab->where('status', 'Done');
    //                             });
    //                     });
    //             });
    //     })
    //         ->with([
    //             'transaction' => function ($q) {
    //                 $q->where('status', '!=', 'Complete'); // ✅ exclude completed only
    //             }
    //         ])
    //         ->get([
    //             'id',
    //             'firstname',
    //             'lastname',
    //             'middlename',
    //             'ext',
    //             'birthdate',
    //             'age',
    //             'contact_number',
    //             'barangay'
    //         ]);

    //     // ✅ Save each transaction into Billing table if not already saved
    //     foreach ($patients as $patient) {
    //         foreach ($patient->transaction as $transaction) {
    //             Billing::firstOrCreate(
    //                 [
    //                     'transaction_id' => $transaction->id, // ensure unique per transaction
    //                 ],
    //                 [
    //                     'patient_id' => $patient->id,
    //                     'transaction_number' => $transaction->transaction_number,
    //                     // 'total_amount' => 0, // you can replace with actual computation if needed
    //                 ]
    //             );
    //         }
    //     }

    //     return response()->json($patients);
    // }


    public function TransactionUpdate( Request $request ,$transactionId){

            $validated = $request->validated([
            'status' => 'required|in:Complete'
            ]);

        $transaction = Transaction::findOrFail($transactionId);

        $transaction->update($validated);

        return response()->json($transaction);
    }

}
