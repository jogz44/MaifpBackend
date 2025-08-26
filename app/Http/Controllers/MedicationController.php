<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Laboratory;
use App\Models\Medication;
use App\Models\medoratory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\New_Consultation;
use App\Http\Requests\MedicationRequest;
use App\Http\Requests\medoratoryRequest;
use App\Models\TransactionStatus;

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


    public function store(MedicationRequest $request)
    {
        $validated = $request->validated();

        // Fetch transaction with consultation
        // $transaction = Transaction::with('consultation')
        //     ->findOrFail($validated['transaction_id']);

        //If consultation exists, add it to validated data
        // if ($transaction->consultation) {
        //     $validated['new_consultation_id'] = $transaction->consultation->id;
        // }

        //  current date
        $validated['transaction_date'] = Carbon::now();

        //Create medication with complete data
        $medication = Medication::create($validated);

        return response()->json($medication);
    }



    // this method for the status on the Medication
    public function status(Request $request)
    {
        // ✅ validate request
        $validated = $request->validate([
            'status' => 'required|in:Done,Pending',
            'transaction_id' => 'required|exists:transaction,id',
        ]);

        // ✅ Update or create transaction status
        $transactionStatus = TransactionStatus::updateOrCreate(
            ['transaction_id' => $validated['transaction_id']], // condition
            ['status' => $validated['status']]                 // values to update
        );


        return response()->json([
            'success' => true,
            'message' => 'All Medicine under this transaction updated successfully.',
            'data' => $transactionStatus
        ]);
    }


    // pu

    // public function store(medoratoryRequest $request)
    // {
    //     $validated = $request->validated();

    //     // Check if transaction has consultation
    //     $transaction = \App\Models\Transaction::with('consultation')
    //         ->findOrFail($validated['transaction_id']);

    //     $newConsultationId = $transaction->consultation
    //         ? $transaction->consultation->id
    //         : null;

    //     $meds = [];

    //     foreach ($validated['medoratories'] as $medData) {
    //         $meds[] = medoratory::create([
    //             'transaction_id' => $validated['transaction_id'],
    //             'new_consultation_id' => $newConsultationId, // set only if exists
    //             'medoratory_type' => $medData['medoratory_type'],
    //             'amount' => $medData['amount'],
    //             'status' => $medData['status'] ?? 'Pending',
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'medoratories stored successfully',
    //         'medoratories' => $meds
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
