<?php

namespace App\Http\Controllers;

use App\Http\Requests\lib_doctorRequest;
use App\Models\Patient;
use App\Models\Laboratory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\New_Consultation;
use App\Http\Requests\NewConsultationRequest;
use App\Models\lib_doctor;
use App\Models\Medication;
use App\Models\Medication_details;

class NewConsultationController extends Controller
{
    // // this method for qualiafied for consultation and  will fetch this current date
    // public function qualifiedTransactionsConsultation()
    // {
    //     try {
    //         $patients = Transaction::where('status', 'qualified')
    //             ->where('transaction_type', 'Consultation')
    //             ->whereDate('transaction_date', now()->toDateString()) // ✅ only today's transactions

    //             // exclude patients who already have ANY "Done" consultation
    //             ->whereDoesntHave('consultation', function ($query) {
    //                 $query->whereIn('status', ['Done', 'Processing', 'Returned', 'Medication']);
    //             })
    //             ->with([
    //                 'patient',
    //                 'vital',
    //                 'consultation',
    //                 // 'laboratories'
    //             ])
    //             ->get()
    //             ->groupBy('patient_id')
    //             ->map(function ($group) {
    //                 $patient = $group->first()->patient;

    //                 // attach transactions to patient
    //                 $patient->transaction = $group->map(function ($transaction) {
    //                     return collect($transaction)->except('patient');
    //                 })->values();

    //                 return $patient;
    //             })
    //             ->values();

    //         return response()->json($patients);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch qualified transactions.',
    //             'error' => $th->getMessage()
    //         ], 500);
    //     }
    // }
    // this method for qualiafied for consultation and  will fetch this current date
    public function qualifiedTransactionsConsultation()
    {
        try {
            $patients = Transaction::where('status', 'qualified')
                ->where('transaction_type', 'Consultation')
                // ->whereDate('transaction_date', now()->toDateString()) // ✅ only today's transactions

                // exclude patients who already have ANY "Done" consultation
                ->whereDoesntHave('consultation', function ($query) {
                    $query->whereIn('status', ['Done', 'Processing', 'Returned', 'Medication']);
                })
                ->with([
                    'patient',
                    'vital',
                    'consultation',
                    // 'laboratories'
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

            return response()->json($patients);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch qualified transactions.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function ReturnConsultation() // fetching the  patient to the return consultation  consultation->laboratory->consultation
    {
        try {
            $patients = Transaction::whereHas('consultation', function ($query) {
                $query->where('status', 'Returned');
            })
                // ->whereDate('transaction_date', now()->toDateString()) // ✅ today's transactions only
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

    // this line of code  are need to revision
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

    public function lib_doctor_store(lib_doctorRequest $request){

        $validated = $request->validated();
        $doctor = lib_doctor::create($validated);

        return response()->json([
            'message' => 'successfully create',
            'doctor_fee' => $doctor,
        ]);

    }


    public function lib_doctor_update(lib_doctorRequest $request, $lib_doctor)
    {

        $validated = $request->validated();
        $doctor = lib_doctor::findOrFail($lib_doctor);

        $doctor->update($validated);

        return response()->json([
            'message' => 'successfully update',
            'doctor_fee' => $doctor,
        ]);
    }


    public function lib_doctor_delete($lib_doctor)
    {

        $doctor = lib_doctor::findOrFail($lib_doctor);

        $doctor->delete($doctor);

        return response()->json([
            'message' => 'successfully delete',
            'doctor_fee' => $doctor,
        ]);
    }

    public function lib_doctor_index()
    {

        $doctor = lib_doctor::all();

        return response()->json($doctor);
    }
}

