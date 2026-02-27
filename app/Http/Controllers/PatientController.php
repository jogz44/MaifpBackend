<?php

namespace App\Http\Controllers;

use App\Events\BadgeUpdated;
use Exception;
use Carbon\Carbon;
use App\Models\vital;
use App\Models\Patient;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Representative;
use App\Models\vw_patient_billing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PatientRequest;
use App\Models\vw_patient_assessment_maifip;
use App\Models\vw_patient_laboratory;
use App\Models\vw_patient_medication;
use App\Models\vw_patient_consultation;
use App\Models\vw_transaction_complete;
use App\Http\Requests\PatientRequestAll;

use App\Models\vw_patient_assessment_philhealth;

use App\Models\vw_patient_consultation_return;
use App\Services\BadgeService;
use App\Services\PatientService;

class PatientController extends Controller
{
        protected $badgeService;
    protected $patientService;

     public function __construct(BadgeService $badgeService,PatientService $patientService)
         {

            $this->badgeService = $badgeService;
             $this->patientService = $patientService;

         }

         public function test_database(){


        $data = DB::connection('mysql_second_database')
            ->table('tbl_customers')->get();


            return $data;

         }


    public function index()
    {
        // $patients = Cache::rememberForever('patients',function () {
        //     Log::info('📥 Fetching patients from Data base Patients'); // cache miss

        //     return Patient::all();
        // });
        // Log::info('Patients retrieved from cache file'); // always logs
        $patients = Patient::select(
            'id',
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
        )->get();
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
        $patient = Patient::select([
            'id',
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

            'permanent_street',
            'permanent_purok',
            'permanent_barangay',
            'permanent_city',
            'permanent_province',

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
            'user_id'
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




    public function assessment()
    {
        // Fetch data from the view
        $rows = vw_patient_assessment_maifip::all();
            // Log::info('Fetching patients from DB view...'); // debug log

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
                "barangay" => $patient->barangay,
                "transaction_id" => $patient->transaction_id,
                "transaction_status" => $patient->status,
                "maifip" => $patient->maifip,
                "philhealth" => $patient->philhealth,


                // "pernament_street" => $patient->pernament_street,
                // "pernament_purok" => $patient->pernament_purok,
                // "pernament_barangay" => $patient->pernament_barangay,
                // "pernament_city" => $patient->pernament_city,
                // "pernament_province" => $patient->pernament_province,

                // "user_id" => $patient->user_id ?? null,
                // "created_at" => $patient->created_at ?? null,
                // "updated_at" => $patient->updated_at ?? null,
                // "transaction" => $items->map(function ($t) {
                //     return [
                //         "id" => $t->id,
                //         "transaction_number" => $t->transaction_number ?? null,
                //         "patient_id" => $t->patient_id,
                //         "transaction_type" => $t->transaction_type,
                //         "status" => $t->status,
                //         "transaction_date" => $t->transaction_date,
                //         "transaction_mode" => $t->transaction_mode ?? null,
                //         "purpose" => $t->purpose ?? null,
                //         "created_at" => $t->created_at ?? null,
                //         "updated_at" => $t->updated_at ?? null,
                //         "representative_id" => $t->representative_id ?? null,
                //     ];
                // })->values()
            ];
        })->values();

        return response()->json($patients);
    }



    public function philhealth_to_maifip_assessment()
    {
        // Fetch data from the view
        $rows = DB::table('vw_patient_assessment_philhealth_to_maifip')->get();
        // Log::info('Fetching patients from DB view...'); // debug log

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
                "barangay" => $patient->barangay,


            ];
        })->values();

        return response()->json($patients);
    }


    public function philhealth_assessment()
    {
        // Fetch data from the view
        $rows = DB::table('vw_patient_philhealth')->get();
        // Log::info('Fetching patients from DB view...'); // debug log

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
                "barangay" => $patient->barangay,
                "transaction_id" => $patient->transaction_id,


            ];
        })->values();

        return response()->json($patients);
    }


    public function storePatient(PatientRequestAll $request)
    {
        // ✅ 1. Prepare Patient Data
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
            'permanent_street',
            'permanent_purok',
            'permanent_barangay',
            'permanent_city',
            'permanent_province',
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
            'is_solo'
        ]);

        $representativeData = $request->only([
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


        $transactionData = $request->only([
            'transaction_type',
            'transaction_date',
            'transaction_mode',
            'purpose',
            'status'
        ]);


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

        $result = $this->patientService->store($patientData,$representativeData,$transactionData,$vitalData,$request);


        // ✅ Then broadcast the fresh counts AFTER the DB has changed
        $counts = app(BadgeService::class)->getBadgeCounts();
        broadcast(new BadgeUpdated($counts));


        return $result;


    }


    public function update(PatientRequest $request, $id)
    {
        $validated = $request->validated();

        $patient = Patient::findOrFail($id);

        // Save old values before update
        $oldValues = $patient->getOriginal();

        // Perform update
        $patient->update($validated);


        // ✅ Then broadcast the fresh counts AFTER the DB has changed
        $counts = app(BadgeService::class)->getBadgeCounts();
        broadcast(new BadgeUpdated($counts));


        // 🗑️ Clear cache so next index() fetch is fresh
        // Cache::forget('patients');
        // Cache::forget('patients_assessment');
        // Log::info("🗑️ Cache cleared after updating patient ID {$id}");

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
    //     // ✅ Count of assessed patients (unique)
    //     $count_assessment = vw_patient_assessment_maifip::distinct('patient_id')->count('patient_id');

    //     // ✅ Count of qualified consultations (unique patients)
    //     $count_consultation = vw_patient_consultation::distinct('patient_id')->count('patient_id');

    //     // ✅ Laboratory count (unique patients)
    //     $count_laboratory = vw_patient_laboratory::distinct('patient_id')->count('patient_id');

    //     // ✅ Medication count (unique patients)
    //     $count_medication = vw_patient_medication::distinct('patient_id')->count('patient_id');

    //     // ✅ Returned consultations (unique patients)
    //     $count_return_consultation = vw_patient_consultation_return::distinct('patient_id')->count('patient_id');

    //     // ✅ Billing patients (unique)
    //     $count_billing = vw_patient_billing::distinct('patient_id')->count('patient_id');

    //     // ✅ Guarantee letter patients (unique)
    //     $count_guarantee = vw_transaction_complete::distinct('patient_id')->count('patient_id');


    //     // $count_assessment_philhealth_to_maifip = vw_patient_assessment_philhealth_to_maifip::distinct('patient_id')->count('patient_id');

    //     $count_assessment_philhealth= vw_patient_assessment_philhealth::distinct('patient_id')->count('patient_id');


    //     return response()->json([
    //         'totalAssessedCount'   => $count_assessment,
    //         'totalQualifiedCount'  => $count_consultation,
    //         'totalLaboratoryCount' => $count_laboratory,
    //         'totalMedicationCount' => $count_medication,
    //         'totalReturnedCount'   => $count_return_consultation,
    //         'totalBillingCount'    => $count_billing,
    //         'totalGLCount'         => $count_guarantee,
    //         // 'totalphilhealth_to_maifip'         => $count_assessment_philhealth_to_maifip,
    //         'totalphilhealth'         => $count_assessment_philhealth,

    //     ]);
    // }

    public function total_count_badge()
    {
        $result = $this->badgeService->getBadgeCounts();

        broadcast(new BadgeUpdated($result)); // remove toOthers() so sender also gets it

        return response()->json($result);
    }
}
