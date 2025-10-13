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
        // ✅ Validate inputs
        $validated = $request->validate([
            'gl_number'   => 'required|string|unique:assistances,gl_number,' . $transaction_id . ',transaction_id',
            'fund_source' => 'required|string',
            'fund_amount' => 'required|numeric',
            'transaction_id' => 'required|exists:transaction,id',


            'consultation_amount' => 'nullable|numeric',

            'medication_total' => 'nullable|numeric',
            'total_billing' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'final_billing' => 'nullable|numeric',


            'radiology_total' => 'nullable|numeric',
            'examination_total' => '|nullable|numeric',
            'ultrasound_total' => 'nullable|numeric',
            'mammogram_total' => 'nullable|numeric',


            'medication' => 'nullable|array',
            'medication.*.item_description' => 'nullable|string',
            'medication.*.quantity' => 'nullable|integer',
            'medication.*.unit' => 'nullable|string',
            'medication.*.amount' => 'nullable|numeric',
            'medication.*.total' => 'nullable|numeric',
            'medication.*.transaction_date' => 'nullable|date',

            'ultrasound_details' => 'nullable|array',
            'ultrasound_details.*.body_parts' => 'nullable|string',
            'ultrasound_details.*.total_amount' => 'nullable|numeric',

            'mammogram_details' => 'nullable|array',
            'mammogram_details.*.procedure' => 'nullable|string',
            'mammogram_details.*.total_amount' => 'nullable|numeric',

            'radiology_details' => 'nullable|array',
            'radiology_details.*.item_description' => 'nullable|string',
            'radiology_details.*.total_amount' => 'nullable|numeric',

            'examination_details' => 'nullable|array',
            'examination_details.*.item_description' => 'nullable|string',
            'examination_details.*.total_amount' => 'nullable|numeric',

        ]);
        // 🧹 Helper function for clean encoding
        $encodeIfNotEmpty = fn($arr) => (!empty($arr) && count(array_filter($arr, fn($v) => !empty($v))) > 0)
            ? json_encode($arr)
            : null;

        // ✅ Find Assistance record by transaction_id or create a new one
        $assistance = Assistances::UpdateOrCreate(
            ['transaction_id' => $transaction_id],
            [
                'gl_number'            => $validated['gl_number'],
                'consultation_amount'  => $validated['consultation_amount'],
                'mammogram_total'      => $validated['mammogram_total'],
                'radiology_total'      => $validated['radiology_total'],
                'examination_total'    => $validated['examination_total'],
                'ultrasound_total'     => $validated['ultrasound_total'],
                'medication_total'      => $validated['medication_total'],
                'total_billing'        => $validated['total_billing'],
                'discount'             => $validated['discount'],
                'final_billing'        => $validated['final_billing'],

                'ultrasound_details'  => $encodeIfNotEmpty($validated['ultrasound_details'] ?? []),
                'mammogram_details'   => $encodeIfNotEmpty($validated['mammogram_details'] ?? []),
                'radiology_details'   => $encodeIfNotEmpty($validated['radiology_details'] ?? []),
                'examination_details' => $encodeIfNotEmpty($validated['examination_details'] ?? []),
                'medication'          => $encodeIfNotEmpty($validated['medication'] ?? []),
            ] // if creating new, set GL number
        );

        // ✅ If the Assistance already exists, update gl_number
        if (!$assistance->wasRecentlyCreated) {
            $assistance->update([
                'gl_number' => $validated['gl_number'],
            ]);
        }
            // Create new fund if not exists
            $assistance->funds()->create([
                'fund_source' => $validated['fund_source'],
                'fund_amount' => $validated['fund_amount'],

            ]);

        return response()->json([
            'message'    => 'Successfully created/updated assistance and fund source',
            'assistance' => $assistance->load('funds'),
        ]);
    }

    public function update_status(Request $request, $transaction_id) // updating the transaction status to Funded
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
