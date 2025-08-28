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

    public function index()
    {

    $transaction = Transaction::with('laboratories')->get();

    return response()->json($transaction);

     }


    public function show($id)
    {
        // Logic to fetch a transaction by ID
        $transaction = Transaction::with(['vital','laboratories','representative'])->find($id);

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



}
