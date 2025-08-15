<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Requests\VitalRequest;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\vital;
use Illuminate\Http\Request;

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

    // public function qualifiedTransactions()
    // {
    //     try {
    //         $transactions = Transaction::where('status', 'qualified')->with('patient')->get();

    //         return response()->json([
    //             'success' => true,
    //             'transactions' => $transactions
    //         ]);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch qualified transactions.',
    //             'error' => $th->getMessage()
    //         ], 500);
    //     }
    // }
    // public function qualifiedTransactions()
    // {
    //     try {
    //         $transactions = Transaction::where('status', 'qualified')
    //             ->with('patient')
    //             ->get()
    //             ->groupBy('patient_id')
    //             ->map(function ($group) {
    //                 return [
    //                     'patient' => $group->first()->patient,
    //                     'transactions' => $group->map(function ($transaction) {
    //                         return collect($transaction)->except('patient');
    //                     })->values()
    //                 ];
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


    public function qualifiedTransactions()
    {
        try {
            $transactions = Transaction::where('status', 'qualified')
                ->with(['patient' => function ($query) {
                    $query->select('id', 'firstname', 'lastname');
                }])
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
