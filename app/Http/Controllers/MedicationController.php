<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Laboratory;
use App\Models\Medication;
use App\Models\medoratory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\New_Consultation;
use App\Http\Requests\MedicationRequest;
use App\Http\Requests\medoratoryRequest;
use App\Models\Medication_details;
use App\Models\TransactionStatus;

class MedicationController extends Controller
{
    // this method is for Medication will fetch the patient need to Medication
    public function qualifiedTransactionsMedication()
    {
        try {
            $transactions = Transaction::where('status', 'qualified')
                ->where(function ($query) {
                    $query->where('transaction_type', 'Medication')
                        ->orWhereHas('consultation', function ($q) {
                            $q->where('status', 'Medication');
                        });
                })
                ->whereDate('transaction_date', now()->toDateString()) // ✅ today's transactions
                ->whereDoesntHave('medication', function ($q) {
                    $q->where('status', 'Done'); // ❌ exclude if medication is Done
                })
                ->with([
                    'patient',
                    'consultation'
                ])
                ->get()
                ->groupBy('patient_id')
                ->map(function ($group) {
                    $patient = $group->first()->patient;

                    // attach transactions to patient
                    $patient->transaction = $group->map(function ($transaction) {
                        return collect($transaction)->except('patient');
                    })->values();

                    return $patient;
                })
                ->values();

            return response()->json($transactions);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch qualified transactions.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function store(MedicationRequest $request) // 
    {
        $validated = $request->validated();

        // Fetch transaction with consultation
        // $transaction = Transaction::with('consultation')
        //     ->findOrFail($validated['transaction_id']);

        //If consultation exists, add it to validated data
        // if ($transaction->consultation) {
        //     $validated['new_consultation_id'] = $transaction->consultation->id;
        // }

        //  current date
        $validated['transaction_date'] = Carbon::now();

        //Create medication with complete data
        $medication = Medication_details::create($validated);

        return response()->json($medication);
    }



    // this method for the status on the Medication of the transaction_id this method i use for testing
    public function status(Request $request)
    {
        // ✅ validate request
        $validated = $request->validate([
            'status' => 'required|in:Done,Pending',
            'transaction_id' => 'required|exists:transaction,id',
            // 'medication_detials_id' => 'required|exists:medication_details,id'
        ]);

        // ✅ Update or create transaction status
        $transactionStatus = Medication::updateOrCreate(
            ['transaction_id' => $validated['transaction_id']], // condition
            ['status' => $validated['status']]                 // values to update
        );


        return response()->json([
            'success' => true,
            'message' => 'All Medicine under this transaction updated successfully.',
            'data' => $transactionStatus
        ]);
    }

}
