<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\vital;
use App\Models\Patient;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Representative;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

use App\Http\Requests\PatientRequest;

use Illuminate\Database\QueryException;
use App\Http\Requests\PatientRequestAll;
use App\Http\Requests\AddTransactionRequest;
use App\Models\vw_patient_assessment;
use App\Models\vw_patient_billing;
use App\Models\vw_patient_consultation;
use App\Models\vw_patient_consultation_return;
use App\Models\vw_patient_laboratory;
use App\Models\vw_patient_medication;
use App\Models\vw_transaction_complete;
use Illuminate\Validation\ValidationException;


class PatientController extends Controller
{



    //fetch all patients
    public function index()
    {
        $patients = Patient::all();

        return response()->json($patients);
    }


    public function getAllPatientsWithLatestTransaction()
    {
        $patients = Patient::select('id', 'firstname', 'middlename', 'lastname', 'ext', 'gender', 'age','contact_number')
            ->with([
                'latestTransaction.consultation:id,transaction_id,status',
                'latestTransaction.laboratories:id,transaction_id,status',
                'latestTransaction.medication:id,transaction_id,status',
            // 'latestTransaction.guaranteeLetter:id,transaction_id,status',

            ])
            ->get();

        return response()->json($patients);
    }

    // for transaction of the patient
    public function show($id, Request $request)
    {
        $user = Auth::user();
        $patient = Patient::select(['id','lastname','firstname','contact_number',
        'middlename','ext','gender','age','ext','birthdate','category','purok','street','barangay','city',
            'philsys_id',
            'philhealth_id',
            'place_of_birth',
            'civil_status',
            'religion',
            'education',
            'occupation',
            'income',

            'permanent_street',
            'permanent_purok',
            'permanent_barangay',
            'permanent_city',
            'permanent_province',
        ])
        ->with('transaction')->find($id);

        // ✅ Patient full name
        $patientName = trim("{$patient->firstname} {$patient->middlename} {$patient->lastname} {$patient->ext}");

        // ✅ Activity log
        $actorName = $user ? "{$user->first_name} {$user->last_name}" : 'System';

        activity($actorName)
            ->causedBy($user)
            ->performedOn($patient)
            ->withProperties([
                'ip'   => $request->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
            ])
            ->log("Viewed record of Patient: {$patientName} (ID: {$patient->id})");
        return response()->json($patient);
    }


    // public function assessment()
    // {
    //     $patients = Patient::whereHas('transaction', function ($query) {
    //         $query->where('status', 'assessment');

    //     })
    //         ->with(['transaction' => function ($query) {
    //             $query->where('status', 'assessment');

    //         }])
    //         ->get();

    //     return response()->json($patients);
    // }




    public function assessment()
    {
        // Fetch data from the view
        $rows = vw_patient_assessment::all();

        // Group by patient_id
        $grouped = $rows->groupBy('patient_id');

        // Transform into desired structure
        $patients = $grouped->map(function ($items) {
            $patient = $items->first(); // patient details (same for all rows)

            return [
                "id" => $patient->patient_id,
                "firstname" => $patient->firstname,
                "lastname" => $patient->lastname,
                "middlename" => $patient->middlename,
                "ext" => $patient->ext,
                "birthdate" => $patient->birthdate,
                "contact_number" => $patient->contact_number,
                "age" => $patient->age,
                "gender" => $patient->gender,
                "is_not_tagum" => $patient->is_not_tagum,
                "street" => $patient->street,
                "purok" => $patient->purok,
                "barangay" => $patient->barangay,
                "city" => $patient->city,
                "province" => $patient->province,
                "category" => $patient->category,
                "philsys_id" =>$patient->philsys_id,
                "philhealth_id"  => $patient->philhealth_id,
                "place_of_birth"  => $patient->place_of_birth,
                "civil_status"  => $patient->civil_status,
                "religion"  => $patient->religion,
                "education"  => $patient->education,
                "occupation"  => $patient->occupation,
                "income"  => $patient->income,
                "is_pwd" => $patient->is_pwd,
                "is_solo" => $patient->is_solo,


                "pernament_street" => $patient->pernament_street,
                "pernament_purok" => $patient->pernament_purok,
                "pernament_barangay" => $patient->pernament_barangay,
                "pernament_city" => $patient->pernament_city,
                "pernament_province" => $patient->pernament_province,

                "user_id" => $patient->user_id ?? null,
                "created_at" => $patient->created_at ?? null,
                "updated_at" => $patient->updated_at ?? null,
                "transaction" => $items->map(function ($t) {
                    return [
                        "id" => $t->id,
                        "transaction_number" => $t->transaction_number ?? null,
                        "patient_id" => $t->patient_id,
                        "transaction_type" => $t->transaction_type,
                        "status" => $t->status,
                        "transaction_date" => $t->transaction_date,
                        "transaction_mode" => $t->transaction_mode ?? null,
                        "purpose" => $t->purpose ?? null,
                        "created_at" => $t->created_at ?? null,
                        "updated_at" => $t->updated_at ?? null,
                        "representative_id" => $t->representative_id ?? null,
                    ];
                })->values()
            ];
        })->values();

        return response()->json($patients);
    }

        // public function assessment()
        // {
        //     // Fetch data from the view
        //     $rows = vw_patient_assessment::all();


        //     return response()->json($rows);

        // }

    public function storeAll(PatientRequestAll $request)
    {
        // $userId = Auth::id();
        $user = Auth::user();

        try {
            // ✅ Patient data
            $patientData = $request->only([
                'firstname',
                'lastname',
                'middlename',
                'ext',
                'birthdate',
                'contact_number',
                'age',
                'gender',
                'is_not_tagum',
                'street',
                'purok',
                'barangay',
                'city',
                'province',
                'category',
                'philsys_id',
                'philhealth_id',
                'place_of_birth',
                'civil_status',
                'religion',
                'education',
                'occupation',
                'income',
                'is_pwd',
                'is_solo',

                'permanent_street',
                'permanent_purok',
                'permanent_barangay',
                'permanent_city',
                'permanent_province',

            ]);

            // ✅ Check if patient already exists
            $existingPatient = Patient::where('firstname', $patientData['firstname'])
                ->where('lastname', $patientData['lastname'])
                ->where('birthdate', $patientData['birthdate'])
                ->first();

            if ($existingPatient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient already has a record. Please add a new transaction instead.',
                    'patient' => $existingPatient
                ], 409);
            }

            // ✅ Add logged-in user ID
            // $patientData['user_id'] = $userId;

            // ✅ Create new patient
            $patient = Patient::create($patientData);

            $representativeData  = $request->only([
                'rep_name',
                'rep_relationship',
                'rep_contact',
                'rep_barangay',
                'rep_address',
                'rep_purok',
                'rep_street',
                'rep_city',
                'rep_province'
            ]);

            $representative = Representative::create($representativeData);

            // ✅ Generate transaction number
            $datePart = now()->format('Y-m-d');
            $sequenceFormatted = str_pad($patient->id, 5, '0', STR_PAD_LEFT);
            $transactionNumber = "{$datePart}-{$sequenceFormatted}";

            // ✅ Transaction data
            $transactionData = $request->only([
                'transaction_type',
                'transaction_date',
                'transaction_mode',
                'purpose'
            ]);

            $transactionData['patient_id'] = $patient->id;
            $transactionData['representative_id'] = $representative->id;
            $transactionData['transaction_number'] = $transactionNumber;
            $transaction = Transaction::create($transactionData);

            // ✅ Vital signs
            $vitalData = $request->only([
                'height',
                'weight',
                'bmi',
                'temperature',
                'waist',
                'pulse_rate',
                'sp02',
                'heart_rate',
                'blood_pressure',
                'respiratory_rate',
                'medicine',
                'LMP'
            ]);

            $vitalData['patient_id'] = $patient->id;
            $vitalData['transaction_id'] = $transaction->id;
            $vital = Vital::create($vitalData);

            // 📝 Activity Log
            activity($user->first_name . ' ' . $user->last_name)
                ->causedBy($user)
                ->performedOn($patient)
                ->withProperties([
                    'ip' => $request->ip(),
                    'date' => Carbon::now('Asia/Manila')->format('Y-m-d h:i:s A'),
                    'patient' => $patient->toArray(),
                    'representative' => $representative->toArray(),
                    'transaction' => $transaction->toArray(),
                    'vital' => $vital->toArray(),
                ])
                ->log(
                    "Patient record {$patient->firstname} {$patient->lastname} was created "

                );
            return response()->json([
                'success' => true,
                'message' => 'Patient, transaction, and vitals created successfully.',
                'patient' => $patient,
                'transaction' => $transaction,
                'vital' => $vital,
                'representative' => $representative,
                'transaction_number' => $transactionNumber,

            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }
    }


    public function update(PatientRequest $request, $id)
    {
        $validated = $request->validated();

        $patient = Patient::findOrFail($id);

        // Save old values before update
        $oldValues = $patient->getOriginal();

        // Perform update
        $patient->update($validated);

        $user = Auth::user();

        // 📝 Add activity log
        activity($user->username)
            ->causedBy($user) // who updated
            ->performedOn($patient)  // which patient
            ->withProperties([
                'ip' => $request->ip(),
            'date' => Carbon::now('Asia/Manila')->format('Y-m-d h:i:s A'),
            'edited_by' => $user
                ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))
                : ($user->username ?? 'N/A'),

            'old' => $oldValues,
                'new' => $patient->getChanges(),
            ])
            ->log("Patient record {$patient->firstname} {$patient->lastname} was updated");

        return response()->json([
            'success' => true,
            'message' => 'Patient updated successfully',
            'patient' => $patient
        ]);
    }


    // public function total_count_badge()
    // {


    //     // ✅ Count of assessed patients
    //     $count_assessment = Patient::whereHas('transaction', function ($query) {
    //         $query->where('status', 'assessment');
    //             // ->whereDate('transaction_date', $today);
    //     })->count();

    //     // ✅ Count of qualified consultations (unique patients)
    //     $count_consultation = Transaction::where('status', 'qualified')
    //         ->where('transaction_type', 'Consultation')
    //         // ->whereDate('transaction_date', $today)
    //         ->whereDoesntHave('consultation', function ($query) {
    //             $query->whereIn('status', ['Done', 'Processing', 'Returned', 'Medication']);
    //         })
    //         ->distinct('patient_id')
    //         ->count('patient_id');

    //     // ✅ Laboratory count (unique patients)
    //     $count_laboratory = Transaction::where('status', 'qualified')
    //         ->where(function ($query) {
    //             $query->where('transaction_type', 'Laboratory')
    //                 ->orWhereHas('consultation', function ($q) {
    //                     $q->where('status', 'Processing');
    //                 });
    //         })
    //         ->whereDoesntHave('laboratories', function ($lab) {
    //             $lab->where('status', 'Done');
    //         })
    //         // ->whereDate('transaction_date', $today)
    //         ->distinct('patient_id')
    //         ->count('patient_id');

    //     // ✅ Medication count (unique patients)
    //     $count_medication = Transaction::where('status', 'qualified')
    //         ->where(function ($query) {
    //             $query->where('transaction_type', 'Medication')
    //                 ->orWhereHas('consultation', function ($q) {
    //                     $q->where('status', 'Medication');
    //                 });
    //         })
    //         // ->whereDate('transaction_date', $today)
    //         ->whereDoesntHave('medication', function ($q) {
    //             $q->where('status', 'Done');
    //         })
    //         ->distinct('patient_id')
    //         ->count('patient_id');

    //     // ✅ Returned consultations
    //     $count_return_consultation = Transaction::whereHas('consultation', function ($query) {
    //         $query->where('status', 'Returned');
    //     })
    //         // ->whereDate('transaction_date', $today)
    //         ->distinct('patient_id')
    //         ->count('patient_id');

    //     // ✅ Billing patients
    //     $count_billing = Transaction::where('status', '!=', 'Complete')
    //         ->where(function ($q) {
    //             $q->whereHas('consultation', function ($con) {
    //                 $con->where('status', 'Done');
    //             })
    //                 ->orWhere(function ($q2) {
    //                     $q2->whereDoesntHave('consultation')
    //                         ->whereHas('laboratories', function ($lab) {
    //                             $lab->where('status', 'Done');
    //                         });
    //                 })
    //                 ->orWhereHas('medication', function ($med) {
    //                     $med->where('status', 'Done');
    //                 });
    //         })
    //         ->count(); // counts transactions, not patients
    //     // // ✅ Guarantee letter patients
    //     $count_guarantee = Patient::whereHas('transaction', function ($query) {
    //         $query
    //             ->where('status', 'Complete');
    //     }) ->count();

    //     return response()->json([
    //         'totalAssessedCount'   => $count_assessment,
    //         'totalQualifiedCount'  => $count_consultation,
    //         'totalLaboratoryCount' => $count_laboratory,
    //         'totalMedicationCount' => $count_medication,
    //         'totalReturnedCount'   => $count_return_consultation,
    //         'totalBillingCount'    => $count_billing,
    //         'totalGLCount'         => $count_guarantee,
    //     ]);
    // }
    public function total_count_badge()
    {
        // ✅ Count of assessed patients (unique)
        $count_assessment = vw_patient_assessment::distinct('patient_id')->count('patient_id');

        // ✅ Count of qualified consultations (unique patients)
        $count_consultation = vw_patient_consultation::distinct('patient_id')->count('patient_id');

        // ✅ Laboratory count (unique patients)
        $count_laboratory = vw_patient_laboratory::distinct('patient_id')->count('patient_id');

        // ✅ Medication count (unique patients)
        $count_medication = vw_patient_medication::distinct('patient_id')->count('patient_id');

        // ✅ Returned consultations (unique patients)
        $count_return_consultation = vw_patient_consultation_return::distinct('patient_id')->count('patient_id');

        // ✅ Billing patients (unique)
        $count_billing = vw_patient_billing::distinct('patient_id')->count('patient_id');

        // ✅ Guarantee letter patients (unique)
        $count_guarantee = vw_transaction_complete::distinct('patient_id')->count('patient_id');

        return response()->json([
            'totalAssessedCount'   => $count_assessment,
            'totalQualifiedCount'  => $count_consultation,
            'totalLaboratoryCount' => $count_laboratory,
            'totalMedicationCount' => $count_medication,
            'totalReturnedCount'   => $count_return_consultation,
            'totalBillingCount'    => $count_billing,
            'totalGLCount'         => $count_guarantee,
        ]);
    }
}
