<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\vital;
use App\Models\Patient;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\VitalRequest;
use App\Http\Requests\TransactionRequest;

class TransactionController extends Controller
{
    // Add methods for handling transactions here
    // For example, you might have methods to create, update, delete, and fetch transactions

    public function show($id) //this method is for showing a transaction by ID
    {
        // Logic to fetch a transaction by ID
        $transaction = Transaction::with('vital')->find($id);

        return response()->json($transaction);

    }

    public function update(TransactionRequest $request, $id) // this method is for updating transaction
    {
        // Logic to update a transaction
        $validated =  $request->validated();

        $transaction = Transaction::findOrFail($id);
        $transaction->update($validated);

        return response()->json([
            'message' => 'Transaction updated successfully.',
            'transaction' => $transaction]);
    }

    public function vital_update(VitalRequest $request, $id) // this method is for updating vital signs
    {
        // Logic to update a transaction
        $validated =  $request->validated();

        $vital = vital::findOrFail($id);
        $vital->update($validated);

        return response()->json([
            'message' => 'vital updated successfully.',
            'vital' => $vital
        ]);
    }

    public function status_update(Request $request, $id) // this method is for updating transaction status
    {
        // Logic to update a transaction
        $validated =  $request->validate([
            'status' => 'sometimes|required|string|max:255',
        ]);

        $transaction = Transaction::findOrFail($id);
        $transaction->update($validated);

        return response()->json([
            'message' => 'Transaction updated status successfully.',
            'transaction' => $transaction
        ]);
    }

    public function deleteAllTransactions()
    {
        try {
            Patient::query()->delete();

            return response()->json([
                'success' => true,
                'message' => 'All transactions deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transactions.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function qualifiedTransactionsConsultation()
    {
        try {
            $patients = Transaction::where('status', 'qualified')
                ->where('transaction_type', 'Consultation')
                // exclude patients who already have ANY "Done" consultation
                ->whereDoesntHave('consultation', function ($query) {
                      $query->whereIn('status', ['Done', 'Processing', 'Returned']);
                })
                ->with([
                    'patient',
                    'vital',
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

            return response()->json($patients);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch qualified transactions.',
                'error' => $th->getMessage()
            ], 500);
        }
    }




    // public function qualifiedTransactionsConsultation()
    // {
    //     try {
    //         $today = Carbon::today()->toDateString(); // example: "2025-08-19"

    //         $patients = Transaction::where('status', 'qualified')
    //             ->where('transaction_type', 'Consultation')
    //             // exclude patients who already have ANY consultation with status Done, Processing, Returned for today only
    //             ->whereDoesntHave('consultation', function ($query) use ($today) {
    //                 $query->whereDate('consultation_date', $today)
    //                     ->whereIn('status', ['Done', 'Processing', 'Returned']);
    //             })
    //             ->with([
    //                 'patient',
    //                 'vital',
    //                 'consultation'
    //             ])
    //             ->whereDate('transaction_date', $today) // ✅ only today's transactions
    //             ->get()
    //             ->groupBy('patient_id')
    //             ->map(function ($group) {
    //                 $patient = $group->first()->patient;

    //                 // attach transactions to patient
    //                 $patient->transaction = $group->map(function ($transaction) {
    //                     return collect($transaction)->except('patient');
    //                 })->values();

    //                 return $patient;
    //             })
    //             ->values();

    //         return response()->json($patients);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch qualified transactions.',
    //             'error' => $th->getMessage()
    //         ], 500);
    //     }
    // }



    public function qualifiedTransactionsLaboratory()
    {
        try {
            $transactions = Transaction::where('status', 'qualified')
                ->where(function ($query) {
                    $query->where('transaction_type', 'Laboratory')
                        ->orWhereHas('consultation', function ($q) {
                            $q->where('status', 'Processing');
                        });
                })
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
            return response()->json([
                'success' => true,
                'patients' => $transactions
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch qualified transactions.',
                'error' => $th->getMessage()
            ], 500);
        }
    }



    public function qualifiedTransactionsMedication()
    {
        try {
            $transactions = Transaction::where('status', 'qualified')
                ->where('transaction_type', 'Medication')
                ->with([
                    'patient',
                    'vital' // 👈 fetch vitals of the transaction
                ])
                ->get()
                ->groupBy('patient_id')
                ->map(function ($group) {
                    return [
                        'patient' => $group->first()->patient,
                        'transactions' => $group->map(function ($transaction) {
                            return collect($transaction)->except('patient');
                        })->values()
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'patients' => $transactions
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch qualified transactions.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
