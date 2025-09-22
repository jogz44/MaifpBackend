<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Transaction;
use App\Models\vw_patient_billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class BillingController extends Controller
{
    //


    // public function index() // fetching the patient have billing
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
    //                     })
    //                     // ✅ Case 3: Transaction with medication Done
    //                     ->orWhereHas('medication', function ($med) {
    //                         $med->where('status', 'Done');
    //                     });
    //             });
    //     })
    //         ->with([
    //             'transaction' => function ($q) {
    //                 $q->where('status', '!=', 'Complete'); // ✅ exclude completed here too
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
    public function index()
    {
        try {
            $records = vw_patient_billing::all();

            $grouped = $records->groupBy('patient_id')->map(function ($items) {
                $first = $items->first();

                return [
                    'id'             => $first->patient_id,
                    'firstname'      => $first->firstname,
                    'lastname'       => $first->lastname,
                    'middlename'     => $first->middlename,
                    'ext'            => $first->ext,
                    'birthdate'      => $first->birthdate,
                    'age'            => $first->age,
                    'contact_number' => $first->contact_number,
                    'barangay'       => $first->barangay,

                    'transaction' => $items->map(function ($row) {
                        return [
                            'id'                 => $row->transaction_id,
                            'transaction_number' => $row->transaction_number,
                            'patient_id'         => $row->patient_id,
                            'transaction_type'   => $row->transaction_type,
                            'status'             => $row->transaction_status,
                            'transaction_date'   => $row->transaction_date,
                            'transaction_mode'   => $row->transaction_mode,
                            'purpose'            => $row->purpose,
                            'created_at'         => $row->transaction_created_at,
                            'updated_at'         => $row->transaction_updated_at,
                            'representative_id'  => $row->representative_id ?? null,
                        ];
                    })->values()
                ];
            })->values();

            return response()->json($grouped);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch patient billing.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    // fetching the billing of the patient base on his transaction id
    public function billing($transactionId, Request $request)
    {
        $user = Auth::user();
        $transaction = Transaction::with([
            'patient:id,firstname,lastname,age,gender,contact_number,street,purok,barangay,middlename,birthdate,is_pwd,is_solo,category',
            'consultation:id,transaction_id,amount',
            'laboratories_details:id,transaction_id,laboratory_type,amount',
            'medication:id,transaction_id,status',
            'medicationDetails:id,transaction_id,item_description,quantity,unit,amount,patient_id,transaction_date',
            'representative:id,rep_name,rep_relationship,rep_address',
            'assistance.funds:id,assistance_id,fund_source,fund_amount'


        ])->findOrFail($transactionId);

        $consultationAmount = $transaction->consultation?->amount ?? 0;
        $laboratoryTotal    = $transaction->laboratories_details->sum('amount');

        //  Medication calculation (quantity × amount)
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
                    'total'            => $total,         //  quantity × amount
                    'transaction_date' => $med->transaction_date,
                ];
            });

            //  sum of all medication totals
            $medicationTotal = $medicationDetails->sum('total');
        }

        $totalBilling = $consultationAmount + $laboratoryTotal + $medicationTotal;

        //  Apply 20% discount if Senior OR PWD
        $discount = 0;
        $finalBilling = $totalBilling;

        if ($transaction->patient->is_pwd || strtolower($transaction->patient->category) === 'senior') {
            $discount = $totalBilling * 0.20;
            $finalBilling = $totalBilling - $discount;
        }


        //  Patient full name
        $patientName = $transaction->patient
            ? trim("{$transaction->patient->firstname} {$transaction->patient->middlename} {$transaction->patient->lastname}")
            : 'Unknown Patient';

        //  Actor name
        $actorName = $user ? "{$user->first_name} {$user->last_name}" : 'System';

        // Log activity
        activity($actorName)
            ->causedBy($user)
            ->performedOn($transaction)
            ->withProperties([
                'ip'   => $request->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
            ])
            ->log("Viewed billing record for Transaction ID: {$transaction->id}, Patient: {$patientName}");

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
            'laboratories_details'        => $transaction->laboratories_details->map(function ($lab) {
                return [
                    'id'              => $lab->id,
                    'laboratory_type' => $lab->laboratory_type,
                    'amount'          => $lab->amount,
                    'status'          => $lab->status,
                ];
            }),
            'medication'          => $medicationDetails,
            'representative'      => $transaction->representative
                ? [
                    'id'            => $transaction->representative->id,
                    'rep_name'      => $transaction->representative->rep_name,
                    'relationship'  => $transaction->representative->rep_relationship,
                    'address'       => $transaction->representative->rep_address,
                ]
                : null,
            'assistance' => $transaction->assistance ? [
                'id' => $transaction->assistance->id,
                // 'consultation_amount' => $transaction->assistance->consultation_amount,
                // 'laboratory_total'    => $transaction->assistance->laboratory_total,
                // 'medication_total'    => $transaction->assistance->medication_total,
                // 'total_billing'       => $transaction->assistance->total_billing,
                // 'discount'            => $transaction->assistance->discount,
                // 'final_billing'       => $transaction->assistance->final_billing,
                'funds' => $transaction->assistance->funds->map(function ($fund) {
                    return [
                        'id'          => $fund->id,
                        'fund_source' => $fund->fund_source,
                        'fund_amount' => $fund->fund_amount,
                    ];
                }),
            ] : null,
        ]);
    }


    // this function for the update the status of the transaction to complete to proceed on the guarantee
    public function TransactionUpdate(Request $request, $transactionId)
    {
        $user = Auth::user();
        //  Validate request
        $validated = $request->validate([
            'status' => 'required|in:Complete'
        ]);

        // Find transaction with patient info for logging
        $transaction = Transaction::with('patient')->findOrFail($transactionId);

        $oldData = $transaction->toArray();
        $transaction->update($validated);
        $newData = $transaction->toArray();

        // Patient name for better logging
        $patientName = $transaction->patient
            ? $transaction->patient->firstname . ' ' . $transaction->patient->lastname
            : 'Unknown Patient';

        // Activity log
        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($transaction)
            ->withProperties([
                'ip'      => $request->ip(),
                'date'    => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'old'     => $oldData,
                'new'     => $newData,
                'changes' => $validated,
            ])
             ->log("Updated Transaction Status to {$validated['status']} for Patient: {$patientName}");

        return response()->json([
            'message' => 'Transaction updated successfully.',
            'transaction' => $transaction
        ]);
    }
}
