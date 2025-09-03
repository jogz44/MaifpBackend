<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Transaction;
use Illuminate\Http\Request;


class BillingController extends Controller
{
    // //
    // public function billing($transactionId)
    // {
    //     $transaction = Transaction::with([
    //         'patient:id,firstname,lastname,age,gender,contact_number,street,purok,barangay,middlename,birthdate,is_pwd,is_solo,category',
    //         'consultation:id,transaction_id,amount',
    //         'laboratories:id,transaction_id,laboratory_type,amount,status',
    //         'medication:id,transaction_id,status', // ✅ medication status
    //         'medicationDetails:id,transaction_id,item_description,quantity,unit,amount,patient_id,transaction_date',
    //     ])->findOrFail($transactionId);

    //     $consultationAmount = $transaction->consultation?->amount ?? 0;
    //     $laboratoryTotal    = $transaction->laboratories->sum('amount');

    //     // ✅ Check if transaction has Medication with status "Done"
    //     $medicationTotal = 0;
    //     $medicationDetails = [];
    //     if ($transaction->medication && $transaction->medication->status === 'Done') {
    //         $medicationTotal = $transaction->medicationDetails->sum('amount');

    //         $medicationDetails = $transaction->medicationDetails->map(function ($med) {
    //             return [
    //                 'id'              => $med->id,
    //                 'item_description' => $med->item_description,
    //                 'quantity'        => $med->quantity,
    //                 'unit'            => $med->unit,
    //                 'amount'          => $med->amount,
    //                 'transaction_date' => $med->transaction_date,

    //             ];
    //         });
    //     }

    //     $totalBilling = $consultationAmount + $laboratoryTotal + $medicationTotal;

    //     return response()->json([
    //         'patient_id'          => $transaction->patient->id,
    //         'transaction_id'      => $transaction->id,
    //         'transaction_type'    => $transaction->transaction_type,
    //         'firstname'           => $transaction->patient->firstname,
    //         'lastname'            => $transaction->patient->lastname,
    //         'middlename'                 => $transaction->patient->middlename,
    //         'birthdate'                 => $transaction->patient->birthdate,
    //         'age'                 => $transaction->patient->age,
    //         'gender'              => $transaction->patient->gender,
    //         'category'              => $transaction->patient->category,
    //         'is_pwd'              => $transaction->patient->is_pwd,
    //         'is_solo'              => $transaction->patient->is_solo,
    //         'contact_number'      => $transaction->patient->contact_number,
    //         'address'             => [
    //             'street'   => $transaction->patient->street,
    //             'purok'    => $transaction->patient->purok,
    //             'barangay' => $transaction->patient->barangay,
    //         ],
    //         'transaction_date'    => $transaction->transaction_date,
    //         'consultation_amount' => $consultationAmount,
    //         'laboratory_total'    => $laboratoryTotal,
    //         'medication_total'    => $medicationTotal,
    //         'total_billing'       => $totalBilling,
    //         'laboratories'        => $transaction->laboratories->map(function ($lab) {
    //             return [
    //                 'id'              => $lab->id,
    //                 'laboratory_type' => $lab->laboratory_type,
    //                 'amount'          => $lab->amount,
    //                 'status'          => $lab->status,
    //             ];
    //         }),
    //         'medication'  => $medicationDetails,
    //     ]);
    // }
    public function billing($transactionId)
    {
        $transaction = Transaction::with([
            'patient:id,firstname,lastname,age,gender,contact_number,street,purok,barangay,middlename,birthdate,is_pwd,is_solo,category',
            'consultation:id,transaction_id,amount',
            'laboratories:id,transaction_id,laboratory_type,amount,status',
            'medication:id,transaction_id,status',
            'medicationDetails:id,transaction_id,item_description,quantity,unit,amount,patient_id,transaction_date',
        ])->findOrFail($transactionId);

        $consultationAmount = $transaction->consultation?->amount ?? 0;
        $laboratoryTotal    = $transaction->laboratories->sum('amount');

        // ✅ Medication calculation (quantity × amount)
        $medicationTotal = 0;
        $medicationDetails = [];
        if ($transaction->medication && $transaction->medication->status === 'Done') {
            $medicationDetails = $transaction->medicationDetails->map(function ($med) {
                $total = $med->quantity * $med->amount;
                return [
                    'id'               => $med->id,
                    'item_description' => $med->item_description,
                    'quantity'         => $med->quantity,
                    'unit'             => $med->unit,
                    'amount'           => $med->amount,   // per unit price
                    'total'            => $total,         // ✅ quantity × amount
                    'transaction_date' => $med->transaction_date,
                ];
            });

            // ✅ sum of all medication totals
            $medicationTotal = $medicationDetails->sum('total');
        }

        $totalBilling = $consultationAmount + $laboratoryTotal + $medicationTotal;

        // ✅ Apply 20% discount if Senior OR PWD
        $discount = 0;
        $finalBilling = $totalBilling;

        if ($transaction->patient->is_pwd || strtolower($transaction->patient->category) === 'senior') {
            $discount = $totalBilling * 0.20;
            $finalBilling = $totalBilling - $discount;
        }

        return response()->json([
            'patient_id'          => $transaction->patient->id,
            'transaction_id'      => $transaction->id,
            'transaction_type'    => $transaction->transaction_type,
            'firstname'           => $transaction->patient->firstname,
            'lastname'            => $transaction->patient->lastname,
            'middlename'          => $transaction->patient->middlename,
            'birthdate'           => $transaction->patient->birthdate,
            'age'                 => $transaction->patient->age,
            'gender'              => $transaction->patient->gender,
            'category'            => $transaction->patient->category,
            'is_pwd'              => $transaction->patient->is_pwd,
            'is_solo'             => $transaction->patient->is_solo,
            'contact_number'      => $transaction->patient->contact_number,
            'address'             => [
                'street'   => $transaction->patient->street,
                'purok'    => $transaction->patient->purok,
                'barangay' => $transaction->patient->barangay,
            ],
            'transaction_date'    => $transaction->transaction_date,
            'consultation_amount' => $consultationAmount,
            'laboratory_total'    => $laboratoryTotal,
            'medication_total'    => $medicationTotal,
            'total_billing'       => $totalBilling,   // before discount
            'discount'            => $discount,       // ✅ show discount amount
            'final_billing'       => $finalBilling,   // ✅ after discount
            'laboratories'        => $transaction->laboratories->map(function ($lab) {
                return [
                    'id'              => $lab->id,
                    'laboratory_type' => $lab->laboratory_type,
                    'amount'          => $lab->amount,
                    'status'          => $lab->status,
                ];
            }),
            'medication'          => $medicationDetails,
        ]);
    }


    // public function index() //fetching the patient have billing
    // {
    //     $patients = Patient::whereHas('transaction', function ($query) {
    //         $query->whereDate('transaction_date', Carbon::today()) // ✅ only today's transactions
    //             ->where('status', '!=', 'Complete') // ✅ exclude completed transactions
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
    //                     })
    //                     // ✅ Case 3: Transaction with medication Done
    //                     ->orWhereHas('medication', function ($med) {
    //                         $med->where('status', 'Done');
    //                     });
    //             });
    //     })
    //         ->with([
    //             'transaction' => function ($q) {
    //                 $q->whereDate('transaction_date', Carbon::today()) // ✅ eager load only today's transaction
    //                     ->where('status', '!=', 'Complete'); // ✅ exclude completed here too
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
    public function index() // fetching the patient have billing
    {
        $patients = Patient::whereHas('transaction', function ($query) {
            $query->where('status', '!=', 'Complete') // ✅ exclude completed transactions
                ->where(function ($q) {
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
                        })
                        // ✅ Case 3: Transaction with medication Done
                        ->orWhereHas('medication', function ($med) {
                            $med->where('status', 'Done');
                        });
                });
        })
            ->with([
                'transaction' => function ($q) {
                    $q->where('status', '!=', 'Complete'); // ✅ exclude completed here too
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


    public function TransactionUpdate( Request $request ,$transactionId){

            $validated = $request->validated([
            'status' => 'required|in:Complete'
            ]);

        $transaction = Transaction::findOrFail($transactionId);

        $transaction->update($validated);

        return response()->json($transaction);
    }

}
