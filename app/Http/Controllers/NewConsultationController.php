<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\New_Consultation;
use App\Http\Requests\NewConsultationRequest;

class NewConsultationController extends Controller
{
    //

    public function index() // fetching the consultation
    {

        $NewConsultation = New_Consultation::all();

        return response()->json([
            'message' => 'successfully',
            'consultation' => $NewConsultation
        ]);
    }

    public function ReturnConsultation() //result of the consultation for return
    {
        try {
            $patients = Transaction::whereHas('consultation', function ($query) {
                $query->where('status', 'Returned');
            })
                ->with([
                    'patient',
                    'vital',
                    'consultation'
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
                'message' => 'Failed to fetch returned consultations.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {

        $NewConsultation = New_Consultation::findOrFail($id);

        return response()->json([
            'message' => 'successfully',
            'consultation' => $NewConsultation
        ]);
    }



    // public function store(NewConsultationRequest $request){

    //     $validated = $request->validated();
    //     $NewConsultation = New_Consultation::create($validated);

    //     return response()->json([
    //         'message' => 'Successfully Saved',
    //         'consulatation' => $NewConsultation,
    //     ]);

    // }

    public function store(NewConsultationRequest $request) // this  method is for status of the patient if procesing or done 
    {
        $validated = $request->validated();

        // If status is Done, set amount = 500
        if (isset($validated['status']) && $validated['status'] === 'Done') {
            $validated['amount'] = 500;
        }

        $NewConsultation = New_Consultation::create($validated);

        return response()->json([
            'message' => 'Successfully Saved',
            'consultation' => $NewConsultation,
        ]);
    }
}
