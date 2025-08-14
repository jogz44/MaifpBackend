<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    //


    // Add methods for handling transactions here
    // For example, you might have methods to create, update, delete, and fetch transactions

    public function show($id)
    {
        // Logic to fetch a transaction by ID
        $transaction = Transaction::with('vital')->find($id);
        return response()->json($transaction);

    }

    public function update(Request $request, $id)
    {
        // Logic to update a transaction

      $validated =  $request->validate([
            'transaction_type' => 'required|string|max:255',
             'transaction_mode' => 'required|string|max:255',
             'transaction_date' => 'required|date',
             'purpose' => 'nullable|string|max:255',
        ]);
        $transaction = Transaction::findOrFail($id);
        $transaction->update($validated);
        return response()->json([
            'message' => 'Transaction updated successfully.',
            'transaction' => $transaction]);
    }
}
