<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\vital;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Requests\VitalRequest;
use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;

class TransactionController extends Controller
{
    // Add methods for handling transactions here
    // For example, you might have methods to create, update, delete, and fetch transactions


    public function assessment()
    {
        $patients = Patient::whereHas('transaction', function ($query) {
            $query->where('status','assessment')
                ->whereDate('transaction_date', now()->toDateString());
        })
            ->with(['transaction' => function ($query) {
                $query->where('status','assessment')
                    ->whereDate('transaction_date', now()->toDateString());
            }])
            ->get();

        return response()->json($patients);
    }


    public function index(){

    $transaction = Transaction::with('laboratories')->get();

    return response()->json($transaction);


     }

    //this method is for showing a transaction by ID
    // public function show($id)
    // {
    //     // Logic to fetch a transaction by ID
    //     $transaction = Transaction::with('vital')->find($id);

    //     return response()->json($transaction);

    // }


    public function show($id)
    {
        // Logic to fetch a transaction by ID
        $transaction = Transaction::with(['vital','laboratories'])->find($id);

        return response()->json($transaction);

    }

    // this method is for updating transaction
    public function update(TransactionRequest $request, $id)
    {
        // Logic to update a transaction
        $validated =  $request->validated();

        $transaction = Transaction::findOrFail($id);
        $transaction->update($validated);

        return response()->json([
            'message' => 'Transaction updated successfully.',
            'transaction' => $transaction]);
    }

    // this method is for updating vital signs
    public function vital_update(VitalRequest $request, $id)
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

    // this method is for updating transaction status if the patient are qualified or unqualified
    public function status_update(Request $request, $id)
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

    //deleting the AllTransactions Data
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

    // this method for qualiafied for consultation and  will fetch this current date
    public function qualifiedTransactionsConsultation(){
        try {
            $patients = Transaction::where('status', 'qualified')
                ->where('transaction_type', 'Consultation')
                ->whereDate('transaction_date', now()->toDateString()) // ✅ only today's transactions

                // exclude patients who already have ANY "Done" consultation
                ->whereDoesntHave('consultation', function ($query) {
                      $query->whereIn('status', ['Done', 'Processing', 'Returned','Medication']);
                })
                ->with([
                    'patient',
                    'vital',
                    'consultation',
                    // 'laboratories'
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



    // this method is for laboratory will fetch the patient need to laboratory
    // public function qualifiedTransactionsLaboratory()
    // {
    //     try {
    //         $transactions = Transaction::where('status', 'qualified')
    //             ->where(function ($query) {
    //                 $query->where('transaction_type', 'Laboratory')
    //                     ->orWhereHas('consultation', function ($q) {
    //                         $q->where('status', 'Processing');
    //                     });
    //             })
    //             ->whereDate('transaction_date', now()->toDateString()) // ✅ per transaction date (today)

    //             ->with([
    //                 'patient',
    //                 'vital',       // fetch vitals of the transaction
    //                 'consultation',
    //                 'laboratories' // fetch laboratory
    //             ])
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
    //         return response()->json([
    //             'success' => true,
    //             'patients' => $transactions
    //         ]);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch qualified transactions.',
    //             'error' => $th->getMessage()
    //         ], 500);
    //     }
    // }

    // this method is for laboratory will fetch the patient need to laboratory
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
                // ❌ Exclude transactions that already have laboratories with status = 'Done'
                ->whereDoesntHave('laboratories', function ($lab) {
                    $lab->where('status', 'Done');
                })
                ->whereDate('transaction_date', now()->toDateString()) // ✅ per transaction date (today)
                ->with([
                    'patient',
                    'vital',       // fetch vitals of the transaction
                    'consultation',
                    'laboratories' // fetch laboratories
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
}
