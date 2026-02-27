<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Representative;
use App\Models\Transaction;
use App\Models\vital;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PatientService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }


    public function store($patientData, $representativeData, $transactionData, $vitalData,$request)
    {
        $user = Auth::user();

        try {



            // ✅ 2. Check for Existing Patient
            $existingPatient = Patient::where('firstname', $patientData['firstname'])
                ->where('lastname', $patientData['lastname'])
                ->where('birthdate', $patientData['birthdate'])
                ->first();

            if ($existingPatient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient already exists. Please add a new transaction instead.',
                    'patient' => $existingPatient
                ], 409);
            }

            // ✅ 3. Create Patient
            $patient = Patient::create($patientData);


            $firstname = $patientData['firstname'] ?? null;
            $lastname = $patientData['lastname'] ?? null;
            $birthdate = $patientData['birthdate'] ?? null;

            $existingCustomers = DB::connection('mysql_second_database')
                ->table('tbl_customers')
                ->where('firstname', $firstname)
                ->where('lastname', $lastname)
                ->where('birthdate', $birthdate)
                ->first();

            if ($existingCustomers) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient already exists. Please add a new transaction instead.',
                    'patient' => $existingCustomers
                ], 409);
            }

           $data = DB::connection('mysql_second_database')
                ->table('tbl_customers')
                ->insert([
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'middlename' => $patientData['middlename'] ?? null,
                    'ext' => $patientData['ext'] ?? null,
                    'birthdate' => $birthdate,
                    'contact_number' => $patientData['contact_number'] ?? null,
                    'age' => $patientData['age'] ?? null,
                    'gender' => $patientData['gender'] ?? null,
                    'is_not_tagum' => $patientData['is_not_tagum'] ?? 0,
                    'street' => $patientData['street'] ?? null,
                    'purok' => $patientData['purok'] ?? null,
                    'barangay' => $patientData['barangay'] ?? null,
                    'city' => $patientData['city'] ?? null,
                    'province' => $patientData['province'] ?? null,
                    'category' => $patientData['category'] ?? null,
                    'is_pwd' => $patientData['is_pwd'] ?? 0,
                    'is_solo' => $patientData['is_solo'] ?? 0,
                    'origin' => 'MAIFP',
                    'maifp_id' => $patient->id,
                    'user_id' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);




            // ✅ 4. Create Representative

            $representative = Representative::create($representativeData);

            // ✅ 5. Generate Transaction Number
            $datePart = now()->format('Y-m-d');
            $sequenceFormatted = str_pad($patient->id, 5, '0', STR_PAD_LEFT);
            $transactionNumber = "{$datePart}-{$sequenceFormatted}";



            // ✅ 7. Prepare Transaction Data
            $transactionData = $request->only([
                'transaction_type',
                'transaction_date',
                'transaction_mode',
                'purpose',
                'status'
            ]);

            $status = $transactionData['status'] ?? 'not started';

            $transactionData['patient_id'] = $patient->id;
            $transactionData['representative_id'] = $representative->id;
            $transactionData['transaction_number'] = $transactionNumber;
            $transactionData['status'] = $status; // ✅ make sure "Pending" exists in enum

            $transaction = Transaction::create($transactionData);

            // ✅ 8. Create Vital Signs

            $vitalData['patient_id'] = $patient->id;
            $vitalData['transaction_id'] = $transaction->id;
            $vital = vital::create($vitalData);

            // ✅ 9. Log Activity
            activity($user->first_name . ' ' . $user->last_name)
                ->causedBy($user)
                ->performedOn($patient)
                ->withProperties([
                    'ip' => $request->ip(),
                    'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                    'patient' => $patient->toArray(),
                    'representative' => $representative->toArray(),
                    'transaction' => $transaction->toArray(),
                    'vital' => $vital->toArray(),
                ])
                ->log("Created patient record for {$patient->firstname} {$patient->lastname}");

            // ✅ 10. Success Response
            return response()->json([
                'success' => true,
                'message' => 'Patient, transaction, and vitals created successfully.',
                'patient' => $patient,
                'transaction_id' => $transaction->id,
                'transaction' => $transaction,
                'vital' => $vital,
                'representative' => $representative,
                'transaction_number' => $transactionNumber,
                'philsys_patient_store' => $data,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage(),
            ], 500);
        }
    }



}
