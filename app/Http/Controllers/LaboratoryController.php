<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\New_Consultation;
use App\Http\Requests\LaboratoryRequest;
use App\Http\Requests\lib_laboratoryRequest;
use App\Models\lib_laboratory;
use Illuminate\Auth\Events\Validated;

class LaboratoryController extends Controller
{
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
                // ->whereDate('transaction_date', now()->toDateString()) // ✅ per transaction date (today)
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

            return response()->json($transactions);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch qualified transactions.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

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

    public function store(LaboratoryRequest $request) //  this method is for saving the laboratory of the patient will his transaction with amount
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



    //for library laboratory store

    public function lib_laboratory_store(lib_laboratoryRequest $request)
    {
        $validated = $request->validated();

        // Check if laboratory already exists
        $existing = lib_laboratory::where('lab_name', $validated['lab_name'])->first();

        if ($existing) {
            return response()->json([
                'message' => 'already exists',
                'laboratory' => $existing,
            ], 200);
        }

        // Create new if not exists
        $laboratory = lib_laboratory::create($validated);

        return response()->json([
            'message' => 'success',
            'laboratory' => $laboratory,
        ]);
    }


    public function lib_laboratory_update(lib_laboratoryRequest $request, $lib_laboratory)
    {
        $validated = $request->validated();

        $laboratory = lib_laboratory::findOrFail($lib_laboratory);

        $laboratory->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated',
            'data' => $laboratory,
        ]);
    }




    public function lib_laboratory_delete($lib_laboratory)
    {


        $laboratory = lib_laboratory::findOrFail($lib_laboratory);

        $laboratory->delete($laboratory);

        return response()->json([
            'message' => 'successfully delete',
            'laboratory' => $laboratory,
        ]);
    }

    public function lib_laboratory_index(){

        $laboratory = lib_laboratory::all();

        return response()->json($laboratory);
    }

}
