<?php

namespace App\Http\Controllers;

use App\Models\Assistances;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\vw_patient_billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class BillingController extends Controller
{

    public function index(){

        $billing  = vw_patient_billing::select(
            'patient_id',
            'firstname',
            'lastname',
            'middlename',
            'ext',
            'birthdate',
            'contact_number',
            'age',
            'barangay',
            'transaction_id',
            'transaction_type',
            'transaction_status',
        )->get();

        return response()->json($billing);
    }


    public function billing_report()
    {
        // Get all billing data
        $billing = DB::table('vw_billing_report')->select(
            'patient_id',
            'firstname',
            'lastname',
            'middlename',
            'ext',
            'birthdate',
            'contact_number',
            'age',
            'barangay'
        )->get();

        // Use unique() to filter by patient_id
        $uniquePatients = $billing->unique('patient_id')->values();

        return response()->json($uniquePatients);
    }

    // fetching the billing of the patient base on his transaction id
    public function billing($transactionId, Request $request)
    {
        $user = Auth::user();
        $transaction = Transaction::with([
            'patient:id,firstname,lastname,age,gender,contact_number,street,purok,barangay,middlename,birthdate,is_pwd,is_solo,category,philhealth_id',
            'consultation:id,transaction_id,amount',
            // 'laboratories_details:id,transaction_id,laboratory_type,total_amount',
            'radiologies_details:id,transaction_id,item_description,selling_price,total_amount',
            'ultrasound_details:id,transaction_id,body_parts,rate,service_fee,total_amount',
            'mammogram_details:id,transaction_id,procedure,rate,service_fee,total_amount',
            'examination_details:id,transaction_id,item_id,item_description,selling_price,total_amount',
            'medication:id,transaction_id,status',
            'medicationDetails:id,transaction_id,item_description,quantity,unit,amount,patient_id,transaction_date',
            'representative:id,rep_name,rep_relationship,rep_address',
            'assistance.funds:id,assistance_id,fund_source,fund_amount'
        ])->findOrFail($transactionId);

        $consultationAmount = $transaction->consultation?->amount ?? 0;
        // $laboratoryTotal    = $transaction->laboratories_details->sum('total_amount');
        $radiologyTotal    = $transaction->radiologies_details->sum('total_amount');
        $examinationTotal    = $transaction->examination_details->sum('total_amount');
        $ultrasoundTotal    = $transaction->ultrasound_details->sum('total_amount');
        $mammogramTotal    = $transaction->mammogram_details->sum('total_amount');

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

        $totalBilling = $consultationAmount  + $medicationTotal+ $radiologyTotal+ $examinationTotal + $ultrasoundTotal + $mammogramTotal;

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
            'philhealth_id' => $transaction->patient->philhealth_id,
            'maifip'             => $transaction->maifip,
            'transaction_status'             => $transaction->status,
            'philhealth'             => $transaction->philhealth,

            'address'             => [
                'street'   => $transaction->patient->street,
                'purok'    => $transaction->patient->purok,
                'barangay' => $transaction->patient->barangay,
            ],

            'representative'      => $transaction->representative
                ? [
                    'id'            => $transaction->representative->id,
                    'rep_name'      => $transaction->representative->rep_name,
                    'relationship'  => $transaction->representative->rep_relationship,
                    'address'       => $transaction->representative->rep_address,
                ]
                : null,

            'transaction_date'    => $transaction->transaction_date,
            'consultation_amount' => $consultationAmount,
            'radiology_total'    => $radiologyTotal,
            'ultrasound_total'    => $ultrasoundTotal,
            'examination_total'    => $examinationTotal,
            'mammogram_total'    => $mammogramTotal,
            'medication_total'    => $medicationTotal,
            'total_billing'       => $totalBilling,
            'discount'            => $discount,       //  show discount amount
            'final_billing'       => $finalBilling,

            'radiologies_details'        => $transaction->radiologies_details->map(function ($rad) {
                return [
                    'id'              => $rad->id,
                    'item_description' => $rad->item_description,
                    // 'selling_price' => $rad->selling_price,
                    // 'service_fee' => $rad->service_fee,
                    'total_amount' =>  $rad->total_amount,

                ];
            }),

            'examination_details'        => $transaction->examination_details->map(function ($exam) {
                return [
                    'id'              => $exam->id,
                    'item_id' => $exam->item_id,
                    'item_description' => $exam->item_description,
                    // 'selling_price' => $rad->selling_price,
                    // 'service_fee' => $rad->service_fee,
                    'total_amount' =>  $exam->total_amount,

                ];
            }),

            'mammogram_details'        => $transaction->mammogram_details->map(function ($mammogram) {
                return [
                    'id'  => $mammogram->id,

                    'procedure' => $mammogram->procedure,
                    'rate' => $mammogram->rate,
                    'service_fee' => $mammogram->service_fee,
                    'total_amount' =>  $mammogram->total_amount,

                ];
            }),

            'ultrasound_details'        => $transaction->ultrasound_details->map(function ($ultra) {
                return [
                    'id'  => $ultra->id,
                    'body_parts' => $ultra->body_parts,
                    'rate' => $ultra->rate,
                    'service_fee' => $ultra->service_fee,
                    'total_amount' =>  $ultra->total_amount,

                ];
            }),
            'medication'          => $medicationDetails,

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
