<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Laboratory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\New_Consultation;
use App\Http\Requests\NewConsultationRequest;
use App\Models\Medication;
use App\Models\Medication_details;

class NewConsultationController extends Controller
{
    //

    // public function index() // fetching the consultation
    // {

    //     $NewConsultation = New_Consultation::all();

    //     return response()->json([
    //         'message' => 'successfully',
    //         'consultation' => $NewConsultation
    //     ]);
    // }

    public function ReturnConsultation()
    {
        try {
            $patients = Transaction::whereHas('consultation', function ($query) {
                $query->where('status', 'Returned');
            })
                ->whereDate('transaction_date', now()->toDateString()) // ✅ today's transactions only
                ->with([
                    'patient',
                    'vital',
                    'consultation',
                    'laboratories'
                ])
                ->get()
                ->groupBy('patient_id')
                ->map(function ($group) {
                    $patient = $group->first()->patient;

                    // attach transactions WITH labs (don’t exclude them)
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
                'message' => 'Failed to fetch returned consultations.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    // public function show($id)
    // {

    //     $NewConsultation = New_Consultation::findOrFail($id);

    //     return response()->json([
    //         'message' => 'successfully',
    //         'consultation' => $NewConsultation
    //     ]);
    // }

    // public function store(NewConsultationRequest $request) //  this method have 2 condition update and create the consultation of the patient
    // {
    //     $validated = $request->validated();

    //     // If status is Done, set amount = 500
    //     if (isset($validated['status']) && $validated['status'] === 'Done') {
    //         $validated['amount'] = 500;
    //     }

    //     // ✅ Update if transaction_id exists, otherwise create
    //     $NewConsultation = New_Consultation::updateOrCreate(
    //         ['transaction_id' => $validated['transaction_id']], // match condition
    //         $validated                                          // values to update
    //     );

    //     return response()->json([
    //         'message' => 'Successfully Saved',
    //         'consultation' => $NewConsultation,
    //     ]);
    // }

    // public function store(NewConsultationRequest $request) // store and updating the status
    // {
    //     $validated = $request->validated();

    //     // If status is Done, set amount = 500
    //     if (isset($validated['status']) && $validated['status'] === 'Done') {
    //         $validated['amount'] = 500;
    //     }

    //     // ✅ Update if transaction_id exists, otherwise create
    //     $NewConsultation = New_Consultation::updateOrCreate(
    //         ['transaction_id' => $validated['transaction_id']], // match condition
    //         $validated                                          // values to update
    //     );

    //     // 🔄 Update related Laboratory status
    //     if ($NewConsultation && isset($validated['status'])) {
    //         Laboratory::where('transaction_id', $validated['transaction_id'])
    //             ->update(['status' => $validated['status']]);
    //     }

    //     return response()->json([
    //         'message' => 'Successfully Saved',
    //         'consultation' => $NewConsultation,
    //     ]);
    // }
    public function store(NewConsultationRequest $request) // store and update status
    {
        $validated = $request->validated();

        // If status is Done, set amount = 500
        if (isset($validated['status']) && $validated['status'] === 'Done') {
            $validated['amount'] = 500;
        }

        // ✅ Update if transaction_id exists, otherwise create
        $NewConsultation = New_Consultation::updateOrCreate(
            ['transaction_id' => $validated['transaction_id']], // match condition
            $validated                                          // values to update
        );

        // 🔄 Update related Laboratory status
        if ($NewConsultation && isset($validated['status'])) {
            if ($validated['status'] === 'Medication') {
                // ✅ When consultation status is Medication → lab status becomes Done
                Laboratory::where('transaction_id', $validated['transaction_id'])
                    ->update(['status' => 'Done']);
            } else {
                // otherwise, just mirror the consultation status
                Laboratory::where('transaction_id', $validated['transaction_id'])
                    ->update(['status' => $validated['status']]);
            }
        }

        return response()->json([
            'message' => 'Successfully Saved',
            'consultation' => $NewConsultation,
        ]);
    }

}

