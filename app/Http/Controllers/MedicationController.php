<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\LaboratoryRequest;

class MedicationController extends Controller
{
    //

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
                ->whereDate('transaction_date', now()->toDateString()) // ✅ per transaction date (today)

                ->with([
                    'patient',
                    'vital',       // fetch vitals of the transaction
                    'consultation' // fetch consultation if exists
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
            // return response()->json([
            //     'success' => true,
            //     'patients' => $transactions
            // ]);
            return response()->json($transactions);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch qualified transactions.',
                'error' => $th->getMessage()
            ], 500);
        }
    }



    public function store(LaboratoryRequest $request)
    {
        $validated = $request->validated();

        // Check if transaction has consultation
        $transaction = \App\Models\Transaction::with('consultation')
            ->findOrFail($validated['transaction_id']);

        $newConsultationId = $transaction->consultation
            ? $transaction->consultation->id
            : null;

        $labs = [];

        foreach ($validated['laboratories'] as $labData) {
            $labs[] = Laboratory::create([
                'transaction_id' => $validated['transaction_id'],
                'new_consultation_id' => $newConsultationId, // set only if exists
                'laboratory_type' => $labData['laboratory_type'],
                'amount' => $labData['amount'],
                'status' => $labData['status'] ?? 'Pending',
            ]);
        }

        return response()->json([
            'message' => 'Laboratories stored successfully',
            'laboratories' => $labs
        ]);
    }
}
