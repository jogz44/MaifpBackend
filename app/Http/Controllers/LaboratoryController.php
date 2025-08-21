<?php

namespace App\Http\Controllers;

use App\Http\Requests\LaboratoryRequest;
use App\Models\Laboratory;
use App\Models\New_Consultation;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{

    // this method for the status on the laboratory that have connected on the consultation for the patient
    public function status(Request $request, $transactionId)
    {
        // validate request
        $validated = $request->validate([
            'status' => 'required|in:Done,Returned,Pending'
        ]);

        // find all labs by transaction_id
        $labs = Laboratory::where('transaction_id', $transactionId)->get();

        if ($labs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No laboratories found for this transaction.'
            ], 404);
        }

        // update all labs
        foreach ($labs as $lab) {
            $lab->update($validated);

            // If lab is Returned, also update related consultation
            if ($lab->status === 'Returned' && $lab->new_consultation_id) {
                $consultation = New_Consultation::find($lab->new_consultation_id);

                if ($consultation) {
                    $consultation->status = 'Returned';
                    $consultation->save();
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'All laboratories under this transaction updated successfully.',
            'data' => $labs
        ]);
    }


    // public function store(LaboratoryRequest $request) //  this method is for saving the laboratory of the patient will his transaction with amount
    // {
    //     $validated = $request->validated();

    //     $labs = [];

    //     foreach ($validated['laboratories'] as $labData) {
    //         $labs[] = Laboratory::create([
    //             'transaction_id' => $validated['transaction_id'],
    //             'new_consultation_id' => $validated['new_consultation_id'] ?? null,
    //             'laboratory_type' => $labData['laboratory_type'],
    //             'amount' => $labData['amount'],
    //             'status' => $labData['status'] ?? 'Pending',
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'Laboratories stored successfully',
    //         'laboratories' => $labs
    //     ]);
    // }

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
