<?php

namespace App\Services;

use App\Models\Laboratory;
use App\Models\New_Consultation;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    // this method is for updating transaction status if the patient are qualified or unqualified
    public function update($validated, $id, $request)
    {
        $user = Auth::user();

        $transaction = Transaction::with('patient')->findOrFail($id);

        // Update all related services to Done
        New_Consultation::where('transaction_id', $id)
            ->update(['status' => 'Done']);

        Laboratory::where('transaction_id', $id)
            ->update(['status' => 'Done']);



        $oldData = $transaction->toArray();

        // ✅ Update maifip = true only if status is "Qualified"
        if (strtolower($validated['status']) === 'qualified') {
            $validated['maifip'] = true;
        }

        $transaction->update($validated);

        $newData = $transaction->toArray();

        $patientName = $transaction->patient
            ? $transaction->patient->firstname . ' ' . $transaction->patient->lastname
            : 'Unknown Patient';

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
            ->log("Updated patient transaction status to {$validated['status']} for Patient: {$patientName}");

        return response()->json([
            'message' => 'Transaction updated status successfully.',
            'transaction' => $transaction
        ]);
    }
}
