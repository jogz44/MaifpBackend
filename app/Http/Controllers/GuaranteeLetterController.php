<?php

namespace App\Http\Controllers;


use App\Models\Budget;
use App\Models\Patient;
use App\Models\Assistances;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\GuaranteeLetter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\vw_guarantee_patients;
use App\Http\Requests\GuaranteeLetterRequest;

class GuaranteeLetterController extends Controller
{


    public function guaranteeLetter($transactionId)
    {
        $transaction = Assistances::with([
            'Funds',
            'transaction',
            'patient' => function ($query) {
                $query->select(
                    'id',
                    'lastname',
                    'firstname',
                    'gender',
                    'age',
                    'middlename',
                    'birthdate',
                    'purok',
                    'street',
                    'barangay',
                    'city',
                    'province'
                );
            }
        ])
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        // Decode JSON fields
        $transaction->medication = json_decode($transaction->medication, true);
        $transaction->radiology_details = json_decode($transaction->radiology_details, true);
        $transaction->ultrasound_details = json_decode($transaction->ultrasound_details, true);
        $transaction->mammogram_details = json_decode($transaction->mammogram_details, true);
        $transaction->examination_details = json_decode($transaction->examination_details, true);


        // ✅ Combine purok, street, and barangay into address
        if ($transaction->patient) {
            $transaction->patient->address = trim(
                collect([
                    $transaction->patient->purok,
                    $transaction->patient->street,
                    $transaction->patient->barangay,
                    $transaction->patient->city,
                    $transaction->patient->province,
                ])->filter()->implode(', ')
            );

            // Optionally hide the original fields
            unset($transaction->patient->purok);
            unset($transaction->patient->street);
            unset($transaction->patient->barangay);
            unset($transaction->patient->city);
            unset($transaction->patient->province);
        }

        return response()->json($transaction);
    }

    public function index()
    {
        $transactions = Transaction::where('status', 'Complete')
            ->with(['patient' => function ($query) {
                // Only fetch the patient fields you need
                $query->select(
                    'id', // important! primary key for relationship
                    'firstname',
                    'lastname',
                    'middlename',
                    'ext',
                    'birthdate',
                    'age',
                    'contact_number',
                    'barangay'
                );
            }])
            ->select([
                DB::raw('id as transaction_id'), // alias properly
                'patient_id',
                'transaction_type',
                'transaction_date',
                'status'
            ])
            ->get();

        return response()->json($transactions);
    }

    public function update(Request $request, $transaction_id)
    {
        // ✅ Validation
        $validated = $request->validate([
            'gl_number'   => 'required|string|unique:assistances,gl_number,' . $transaction_id . ',transaction_id',
            'fund_source' => 'required|string',
            'fund_amount' => 'required|numeric',
        ]);

        // ✅ Find Assistance record by transaction_id
        $assistance = Assistances::where('transaction_id', $transaction_id)->first();

        if (!$assistance) {
            return response()->json([
                'message' => "No Assistance record found for transaction_id {$transaction_id}"
            ], 404);
        }

        // ✅ Update gl_number (already validated as unique)
        $assistance->update([
            'gl_number' => $validated['gl_number'],
        ]);

        // ✅ Add fund source
        $assistance->funds()->create([
            'fund_source' => $validated['fund_source'],
            'fund_amount' => $validated['fund_amount'],
        ]);

        return response()->json([
            'message'    => 'Successfully updated assistance and added fund source',
            'assistance' => $assistance->load('funds'),
        ]);
    }

    public function update_status(Request $request, $transaction_id)
    {
        // ✅ Validation
        $validated = $request->validate([
           'status' => 'required|in:Funded'
        ]);

        // ✅ Find Assistance record by transaction_id
        $transaction = Transaction::where('id', $transaction_id)->first();

        if (!$transaction) {
            return response()->json([
                'message' => "No Assistance record found for transaction_id {$transaction_id}"
            ], 404);
        }

        // ✅ Update gl_number (already validated as unique)
        $transaction->update([
            'status' => $validated['status'],
        ]);


        return response()->json([
            'message'    => 'Successfully updated status',
            'transaction' => $transaction,
        ]);
    }
}
