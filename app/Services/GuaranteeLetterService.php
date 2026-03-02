<?php

namespace App\Services;

use App\Models\Assistances;
use App\Models\Transaction;

class GuaranteeLetterService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    // updating the transaction - funded
    public function updateStatus($transactionId,$validated) // updating the transaction status to Funded
    {

        // ✅ Find Assistance record by transaction_id
        $transaction = Transaction::where('id', $transactionId)->first();

        if (!$transaction) {
            return response()->json([
                'message' => "No Assistance record found for transaction_id {$transactionId}"
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

    // updating the assistance
    public function update($validated,$transaction_id)
    {
        // ✅ Validate inputs

        // 🧹 Helper function for clean encoding
        $encodeIfNotEmpty = fn($arr) => (!empty($arr) && count(array_filter($arr, fn($v) => !empty($v))) > 0)
            ? json_encode($arr)
            : null;

        // ✅ Find Assistance record by transaction_id or create a new one
        $assistance = Assistances::UpdateOrCreate(
            ['transaction_id' => $transaction_id],
            [
                'gl_lgu' => $validated['gl_lgu'],
                'gl_cong'            => $validated['gl_cong'],
                'radiology_total' => $validated['radiology_total'] ?? 0,
                'examination_total' => $validated['examination_total'] ?? 0,
                'mammogram_total'   => $validated['mammogram_total'] ?? 0,
                'ultrasound_total'  => $validated['ultrasound_total'] ?? 0,
                'consultation_amount' => $validated['consultation_amount'] ?? 0,
                'medication_total'  => $validated['medication_total'] ?? 0,
                'total_billing'     => $validated['total_billing'] ?? 0,
                'discount'          => $validated['discount'] ?? 0,
                'final_billing'     => $validated['final_billing'] ?? 0,

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
                // 'gl_number' => $validated['gl_number'],
                'gl_lgu' => $validated['gl_lgu'],
                'gl_cong' => $validated['gl_cong'],
            ]);
        }
        // Create new fund if not exists
        if (!empty($request->funds) && is_array($request->funds)) {
            foreach ($request->funds as $fund) {
                $assistance->funds()->create([
                    'fund_source' => $fund['fund_source'],
                    'fund_amount' => $fund['fund_amount'],
                ]);
            }
        }


        return response()->json([
            'message'    => 'Successfully created/updated assistance and fund source',
            'assistance' => $assistance->load('funds'),
        ]);
    }
    

    // get the max gk number
    public function getGLNumber()
    {
        // Get the max value from each column
        $maxLgu = Assistances::max('gl_lgu');
        $maxCong = Assistances::max('gl_cong');

        // Determine which has the higher value
        $maxGLNumber = max((int) $maxLgu, (int) $maxCong);

        // If no records yet, start from 0
        if (!$maxGLNumber) {
            $maxGLNumber = 0;
        }

        // Add 1 and format as 5 digits (e.g., 00001, 00016, etc.)
        $nextNumber = str_pad($maxGLNumber + 1, 5, '0', STR_PAD_LEFT);

        return response()->json(['max_gl_number' => $nextNumber]);
    }
}
