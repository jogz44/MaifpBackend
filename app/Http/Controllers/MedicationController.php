<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\LaboratoryRequest;
use App\Models\Medication;

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
                    // 'vital',       // fetch vitals of the transaction
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


    public function store(Request $request){

        $validated = $request->validated([


        ]);
    }

    // this method for the status on the Medication
    public function status(Request $request, $transactionId)
    {
        // validate request
        $validated = $request->validate([
            'status' => 'required|in:Done,Returned,Pending'
        ]);

        // find all labs by transaction_id
        $labs = Medication::where('transaction_id', $transactionId)->get();

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
                $consultation = Medication::find($lab->new_consultation_id);

                if ($consultation) {
                    $consultation->status = 'Returned';
                    $consultation->save();
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'All Medicine under this transaction updated successfully.',
            'data' => $labs
        ]);
    }


    // pu

    // public function store(LaboratoryRequest $request)
    // {
    //     $validated = $request->validated();

    //     // Check if transaction has consultation
    //     $transaction = \App\Models\Transaction::with('consultation')
    //         ->findOrFail($validated['transaction_id']);

    //     $newConsultationId = $transaction->consultation
    //         ? $transaction->consultation->id
    //         : null;

    //     $labs = [];

    //     foreach ($validated['laboratories'] as $labData) {
    //         $labs[] = Laboratory::create([
    //             'transaction_id' => $validated['transaction_id'],
    //             'new_consultation_id' => $newConsultationId, // set only if exists
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

    // public function sync()
    // {
    //     // fetch data from API
    //     $response = Http::get('http://192.168.100.105:8000/api/patients/consultation/return');

    //     if ($response->successful()) {
    //         $patients = $response->json();

    //         foreach ($patients as $patient) {
    //             SyncedPatient::updateOrCreate(
    //                 [
    //                     'firstname' => $patient['firstname'],
    //                     'lastname'  => $patient['lastname'],
    //                 ],
    //                 [] // you can add extra fields here if needed
    //             );
    //         }

    //         return response()->json(['message' => 'Patients synced successfully']);
    //     }

    //     return response()->json(['message' => 'Failed to fetch API'], 500);
    // }
}
