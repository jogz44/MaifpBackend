<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Patient;
use App\Models\Laboratory;
use App\Models\lib_doctor;
use App\Models\Medication;
use App\Models\Transaction;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\New_;
use App\Models\New_Consultation;
use App\Models\Medication_details;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\vw_patient_consultation;
use App\Http\Requests\lib_doctorRequest;
use App\Http\Requests\NewConsultationRequest;
use App\Models\vw_patient_consultation_return;
use App\Models\vw_patient_laboratory;
use App\Models\vw_patient_medication;

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

    // // this line of code  are need to revision
    // public function store(NewConsultationRequest $request) // store and update status
    // {

    //     $user = Auth::user();
    //     $validated = $request->validated();

    //                 // lab  -  // done  -  // medicine
    //     $status = ['Processing','Done','Medication'];

    //     // If status is Done, fetch doctor_amount
    //     if (isset($validated['status']) && $validated['status'] === 'Done') {
    //         $doctor = lib_doctor::first();
    //         if ($doctor) {
    //             $validated['amount'] = $doctor->doctor_amount;
    //         }
    //     }

    //     // Update if transaction_id exists, otherwise create
    //     $NewConsultation = New_Consultation::updateOrCreate(
    //         ['transaction_id' => $validated['transaction_id']], // match condition
    //         $validated                                          // values to update
    //     );


    //     if ($NewConsultation && isset($validated['status'])) {
    //         $statusToSet = ($validated['status'] === 'Medication') ? 'Done' : $validated['status'];

    //         // Update laboratory by transaction_id
    //         Laboratory::where('transaction_id', $validated['transaction_id'])
    //             ->update(['status' => $statusToSet]);

    //         // Update laboratory by new_consultation_id
    //         Laboratory::where('new_consultation_id', $NewConsultation->id)
    //             ->update(['status' => $statusToSet]);
    //     }

    //     // 📝 Activity Log
    //     $patient = $NewConsultation->patient ?? null;

    //     activity($user->first_name . ' ' . $user->last_name)
    //         ->causedBy($user)
    //         ->performedOn($NewConsultation)
    //         ->withProperties([
    //             'ip' => $request->ip(),
    //             'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
    //             'consultation' => $NewConsultation->toArray(),
    //         ])
    //         ->log("Consultation record for patient " . ($patient ? "{$patient->firstname} {$patient->lastname}" : "Unknown Patient") . " was updated " );

    //     return response()->json([
    //         'message' => 'Successfully Saved',
    //         'consultation' => $NewConsultation,
    //     ]);
    // }

    public function store(NewConsultationRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        /** Define allowed status transitions */
        $allowedStatus = ['Processing', 'Done', 'Medication'];

        // Validate status
        if (!in_array($validated['status'], $allowedStatus)) {
            return response()->json(['error' => 'Invalid status provided.'], 400);
        }

        // Fetch doctor amount (only once)
        $doctorAmount = lib_doctor::value('doctor_amount');

        /** Handle automatic charging based on status */
        if (in_array($validated['status'], ['Processing', 'Done', 'Medication'])) {
            // If amount already exists (from previous record), keep it
            $existing = New_Consultation::where('transaction_id', $validated['transaction_id'])->first();
            if ($existing && $existing->amount > 0) {
                $validated['amount'] = $existing->amount;
            }
            // Else assign doctor amount
            elseif (empty($validated['amount'])) {
                $validated['amount'] = $doctorAmount ?? 0;
            }
        }

        /** Create or Update Consultation */
        $NewConsultation = New_Consultation::updateOrCreate(
            ['transaction_id' => $validated['transaction_id']],
            $validated
        );

        /** Update Laboratory Status */
        if ($NewConsultation) {
            $labStatus = ($validated['status'] === 'Medication') ? 'Done' : $validated['status'];

            Laboratory::where('transaction_id', $validated['transaction_id'])
                ->orWhere('new_consultation_id', $NewConsultation->id)
                ->update(['status' => $labStatus]);
        }

        /** Activity Log */
        $patient = $NewConsultation->patient ?? null;

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($NewConsultation)
            ->withProperties([
                'ip' => $request->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'consultation' => $NewConsultation->toArray(),
            ])
            ->log("Consultation record updated for patient " . ($patient ? "{$patient->firstname} {$patient->lastname}" : "Unknown"));

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

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($doctor)
            ->withProperties([
                'ip' => $request->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'created_by' => $user?->full_name
                    ?? trim($user?->first_name . ' ' . $user?->last_name)
                    ?? $user?->username
                    ?? 'N/A',
                'doctor_fee' => $doctor->toArray(),
            ])
            ->log(
                "Doctor fee  [" . ($doctor ? "{$doctor->doctor_amount}" : "Unknown amount ") . "] was created or update "
            );

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

    public function patients_consultation_list()
    {
        $list = Patient::select(
            'patient.id',
            'patient.firstname',
            'patient.lastname',
            'patient.middlename',
            'patient.ext',
            'patient.birthdate',
            'patient.age',
            'patient.barangay',
            'patient.contact_number',
            'transactions.id as transaction_id'
        )
            ->join('transactions as transactions', 'transactions.patient_id', '=', 'patient.id')
            ->where('transactions.transaction_type', 'Consultation')
            ->get();

        return response()->json($list);
    }


    public function expiredTransactions()
    {
        try {
            $expirationDate = Carbon::now()->subWeeks(2);

            // Process expired consultations
            $expiredConsultations = New_Consultation::whereIn('status', ['Processing','Returned','Medication'])
                ->where('updated_at', '<=', $expirationDate)
                ->get();

            if (!$expiredConsultations->isEmpty()) {
                foreach ($expiredConsultations as $consultation) {
                    $consultation->update(['status' => 'Expired']);

                    if ($consultation->transaction_id) {
                        Transaction::where('id', $consultation->transaction_id)
                            ->update(['status' => 'Expired']);
                    }

                    Log::info("Expired consultation #{$consultation->id}");
                }
                Log::info("Total expired consultations: {$expiredConsultations->count()}");
            }

            // Process expired medications with PRIORITY logic
            // Priority 1: Check consultation_status = 'Medication' first
            // Priority 2: If no consultation_status, check transaction_status = 'Qualified'
            $expiredMedications = vw_patient_medication::where(function ($query) use ($expirationDate) {
                // Priority 1: Has consultation status
                $query->where(function ($subQuery) use ($expirationDate) {
                    $subQuery->where('consultation_status', 'Medication')
                        ->where('consultation_updated_at', '<=', $expirationDate);
                })
                    // Priority 2: No consultation status, check transaction
                    ->orWhere(function ($subQuery) use ($expirationDate) {
                        $subQuery->whereNull('consultation_status') // No consultation
                        ->whereIn('transaction_status', ['Qualified', 'Pending'])
                        ->where('transaction_updated_at', '<=', $expirationDate);
                    });
            })->get();

            if (!$expiredMedications->isEmpty()) {
                $consultationExpired = 0;
                $transactionExpired = 0;

                foreach ($expiredMedications as $medication) {
                    if ($medication->transaction_id) {
                        Transaction::where('id', $medication->transaction_id)
                            ->update(['status' => 'Expired']);

                        // Track which type expired
                        if (!empty($medication->consultation_status)) {
                            $consultationExpired++;
                            Log::info("Expired medication (via consultation) #{$medication->id}, Transaction #{$medication->transaction_id}");
                        } else {
                            $transactionExpired++;
                            Log::info("Expired medication (via transaction) #{$medication->id}, Transaction #{$medication->transaction_id}");
                        }
                    }
                }

                Log::info("Total expired medications: {$expiredMedications->count()} (Consultation: {$consultationExpired}, Transaction: {$transactionExpired})");
            }


            // lab
            $expiredLaboratory = vw_patient_laboratory::where(function ($query) use ($expirationDate) {
                // Priority 1: Has consultation status
                $query->where(function ($subQuery) use ($expirationDate) {
                    $subQuery->where('consultation_status', 'Processing')
                        ->where('consultation_updated_at', '<=', $expirationDate);
                })
                    // Priority 2: No consultation status, check transaction
                    ->orWhere(function ($subQuery) use ($expirationDate) {
                        $subQuery->whereNull('consultation_status') // No consultation
                        ->whereIn('transaction_status', ['Qualified', 'Pending'])
                        ->where('transaction_updated_at', '<=', $expirationDate);
                    });
            })->get();

            if (!$expiredLaboratory->isEmpty()) {
                $consultationExpired = 0;
                $transactionExpired = 0;

                foreach ($expiredLaboratory as $laboratory) {
                    if ($laboratory->transaction_id) {
                        Transaction::where('id', $laboratory->transaction_id)
                            ->update(['status' => 'Expired']);

                        // Track which type expired
                        if (!empty($laboratory->consultation_status)) {
                            $consultationExpired++;
                            Log::info("Expired laboratory (via consultation) #{$laboratory->id}, Transaction #{$laboratory->transaction_id}");
                        } else {
                            $transactionExpired++;
                            Log::info("Expired laboratory (via transaction) #{$laboratory->id}, Transaction #{$laboratory->transaction_id}");
                        }
                    }
                }

                Log::info("Total expired laboratory: {$expiredLaboratory->count()} (Consultation: {$consultationExpired}, Transaction: {$transactionExpired})");
            }

            // Summary log
            $totalExpired = $expiredConsultations->count() + $expiredMedications->count() + $expiredLaboratory->count();

            Log::info("Total items expired: {$totalExpired}");
        } catch (\Exception $e) {
            Log::error('Error in expiredTransactions: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}

