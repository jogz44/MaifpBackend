<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Patient;
use App\Models\Laboratory;
use App\Models\lib_doctor;
use App\Models\Medication;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\New_Consultation;
use App\Models\Medication_details;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\lib_doctorRequest;
use App\Http\Requests\NewConsultationRequest;

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

        $user = Auth::user();
        $validated = $request->validated();

        // If status is Done, set amount = 500
        // if (isset($validated['status']) && $validated['status'] === 'Done') {

        //     $doctor = lib_doctor::where('id','doctor_amount')->firts();

        //     $validated['amount'] = $doctor;
        // }

        // If status is Done, fetch doctor_amount
        if (isset($validated['status']) && $validated['status'] === 'Done') {
            $doctor = lib_doctor::first(); // or lib_doctor::find($validated['doctor_id']);
            if ($doctor) {
                $validated['amount'] = $doctor->doctor_amount;
            }
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
        // 📝 Activity Log
        $patient = $NewConsultation->patient ?? null;

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($NewConsultation)
            ->withProperties([
                'ip' => $request->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'created_by' => $user?->full_name
                    ?? trim($user?->first_name . ' ' . $user?->last_name)
                    ?? $user?->username
                    ?? 'N/A',
                'consultation' => $NewConsultation->toArray(),
            ])
            ->log(
                "Consultation record for patient [" .
                    ($patient ? "{$patient->firstname} {$patient->lastname}" : "Unknown Patient") .
                    "] was created or updated "
                    // ($user?->full_name ?? trim($user?->first_name . ' ' . $user?->last_name) ?? $user?->username ?? 'Unknown')
            );

        return response()->json([
            'message' => 'Successfully Saved',
            'consultation' => $NewConsultation,
        ]);
    }

    public function lib_doctor_store(lib_doctorRequest $request){

        $user = Auth::user();
        $validated = $request->validated();
        $doctor = lib_doctor::create($validated);


        // // 📝 Activity Log

        // activity($user->first_name . ' ' . $user->last_name)
        //     ->causedBy($user)
        //     ->performedOn($doctor)
        //     ->withProperties([
        //         'ip' => $request->ip(),
        //         'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
        //         'created_by' => $user?->full_name
        //             ?? trim($user?->first_name . ' ' . $user?->last_name)
        //             ?? $user?->username
        //             ?? 'N/A',
        //         'doctor_fee' => $doctor->toArray(),
        //     ])
        //     ->log(
        //         "Doctor fee  [" . ($doctor ? "{$doctor->doctor_amount}" : "Unknown amount ") . "] was created or update "
        //     );

        return response()->json([
            'message' => 'successfully create',
            'doctor_fee' => $doctor,
        ]);

    }


    public function lib_doctor_update(lib_doctorRequest $request, $lib_doctor)
    {

        $user = Auth::user();
        $validated = $request->validated();
        $doctor = lib_doctor::findOrFail($lib_doctor);

        $doctor->update($validated);

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($doctor)
            ->withProperties([
                'ip' => $request->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
            ])
            ->log(
                "Doctor fee [" . ($doctor ? "{$doctor->doctor_amount}" : "Unknown amount ") . "] was created or update "
            );
        return response()->json($doctor);
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

