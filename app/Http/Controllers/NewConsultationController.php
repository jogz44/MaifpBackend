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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\lib_doctorRequest;
use App\Http\Requests\NewConsultationRequest;
use App\Models\vw_patient_consultation;
use App\Models\vw_patient_consultation_return;

class NewConsultationController extends Controller
{


    public function qualifiedTransactionsConsultation() //new method
    {
        try {
            $records =vw_patient_consultation::all();

            $grouped = $records->groupBy('patient_id')->map(function ($items) {
                $first = $items->first();

                return [
                    'id'            => $first->patient_id,
                    'firstname'     => $first->firstname,
                    'lastname'      => $first->lastname,
                    'middlename'    => $first->middlename,
                    'ext'           => $first->ext,
                    'birthdate'     => $first->birthdate,
                    'contact_number' => $first->contact_number,
                    'age'           => $first->age,
                    'gender'        => $first->gender,
                    'is_not_tagum'  => $first->is_not_tagum,
                    'street'        => $first->street,
                    'purok'         => $first->purok,
                    'barangay'      => $first->barangay,
                    'city'          => $first->city,
                    'province'      => $first->province,
                    'category'      => $first->category,
                    'is_pwd'        => $first->is_pwd,
                    'is_solo'       => $first->is_solo,
                    'user_id'       => $first->user_id,
                    'created_at'    => $first->patient_created_at,
                    'updated_at'    => $first->patient_updated_at,

                    'transaction'   => $items->map(function ($row) {
                        return [
                            'id'                 => $row->transaction_id,
                            'transaction_number' => $row->transaction_number,
                            'transaction_type'   => $row->transaction_type,
                            'status'             => $row->transaction_status,
                            'transaction_date'   => $row->transaction_date,
                            'transaction_mode'   => $row->transaction_mode,
                            'purpose'            => $row->purpose,
                            'created_at'         => $row->transaction_created_at,
                            'updated_at'         => $row->transaction_updated_at,
                            'representative_id'  => null, // not in view, keep null
                            'vital' => $row->vital_id ? [
                                'id'               => $row->vital_id,
                                'height'           => $row->height,
                                'weight'           => $row->weight,
                                'bmi'              => $row->bmi,
                                'pulse_rate'       => $row->pulse_rate,
                                'temperature'      => $row->temperature,
                                'sp02'             => $row->sp02,
                                'heart_rate'       => $row->heart_rate,
                                'blood_pressure'   => $row->blood_pressure,
                                'respiratory_rate' => $row->respiratory_rate,
                                'medicine'         => $row->medicine,
                                'LMP'              => $row->LMP,
                            ] : null,
                            'consultation' => null, // view doesn’t include consultation details
                        ];
                    })->values()
                ];
            })->values();

            return response()->json($grouped);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch consultation records.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function ReturnConsultation()
    {
        try {
            $records = vw_patient_consultation_return::all();

            $grouped = $records->groupBy('patient_id')->map(function ($items) {
                $first = $items->first();

                return [
                    'id'             => $first->patient_id,
                    'firstname'      => $first->firstname,
                    'lastname'       => $first->lastname,
                    'middlename'     => $first->middlename,
                    'ext'            => $first->ext,
                    'birthdate'      => $first->birthdate,
                    'contact_number' => $first->contact_number,
                    'age'            => $first->age,
                    'gender'         => $first->gender,
                    'is_not_tagum'   => $first->is_not_tagum,
                    'street'         => $first->street,
                    'purok'          => $first->purok,
                    'barangay'       => $first->barangay,
                    'city'           => $first->city,
                    'province'       => $first->province,
                    'category'       => $first->category,
                    'is_pwd'         => $first->is_pwd,
                    'is_solo'        => $first->is_solo,
                    'user_id'        => $first->user_id,
                    'created_at'     => $first->patient_created_at,
                    'updated_at'     => $first->patient_updated_at,

                    'transaction'    => $items->map(function ($row) {
                        return [
                            'id'                 => $row->transaction_id,
                            'transaction_number' => $row->transaction_number,
                            'transaction_type'   => $row->transaction_type,
                            'status'             => $row->transaction_status,
                            'transaction_date'   => $row->transaction_date,
                            'transaction_mode'   => $row->transaction_mode,
                            'purpose'            => $row->purpose,
                            'created_at'         => $row->transaction_created_at,
                            'updated_at'         => $row->transaction_updated_at,
                            'representative_id'  => $row->representative_id,

                            'consultation' => $row->consultation_id ? [
                                'id'     => $row->consultation_id,
                                'status' => $row->consultation_status,
                            ] : null,

                            'vital' => $row->vital_id ? [
                                'id'               => $row->vital_id,
                                'height'           => $row->height,
                                'weight'           => $row->weight,
                                'bmi'              => $row->bmi,
                                'pulse_rate'       => $row->pulse_rate,
                                'temperature'      => $row->temperature,
                                'sp02'             => $row->sp02,
                                'heart_rate'       => $row->heart_rate,
                                'blood_pressure'   => $row->blood_pressure,
                                'respiratory_rate' => $row->respiratory_rate,
                                'medicine'         => $row->medicine,
                                'LMP'              => $row->LMP,
                            ] : null,

                            'laboratory' => $row->laboratory_id ? [
                                'id'         => $row->laboratory_id,
                                'status'     => $row->laboratory_status,
                                'created_at' => $row->laboratory_created_at,
                                'updated_at' => $row->laboratory_updated_at,
                            ] : null,
                        ];
                    })->values()
                ];
            })->values();

            return response()->json($grouped);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch consultation returns.',
                'error'   => $th->getMessage()
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
                'consultation' => $NewConsultation->toArray(),
            ])
            ->log("Consultation record for patient " . ($patient ? "{$patient->firstname} {$patient->lastname}" : "Unknown Patient") . " was updated " );

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

   // updating the library doctor amount  of doctor
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
                "Doctor fee ".($doctor ? "{$doctor->doctor_amount}" : "Unknown amount ")." was updated"
            );
        return response()->json($doctor);
    }

    //deleting the library doctor
    public function lib_doctor_delete($lib_doctor)
    {

        $doctor = lib_doctor::findOrFail($lib_doctor);

        $doctor->delete($doctor);

        return response()->json([
            'message' => 'successfully delete',
            'doctor_fee' => $doctor,
        ]);
    }

    // fetching the library doctor fee
    public function lib_doctor_index()
    {

        $doctor = lib_doctor::all();

        return response()->json($doctor);
    }
}

