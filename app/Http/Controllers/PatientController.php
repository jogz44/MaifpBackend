<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddTransactionRequest;
use App\Http\Requests\PatientRequest;
use App\Models\vital;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Exception;

use Illuminate\Http\Request;

use Illuminate\Database\QueryException;

use Illuminate\Validation\ValidationException;
use PhpParser\Node\Stmt\TryCatch;

class PatientController extends Controller
{

    // fetch all patients
    public function index()
    {
        $patients = Patient::all();

        return response()->json($patients);
    }


        // for transaction of the patient
    public function show($id)
    {
        $patients = Patient::select(['id','lastname','firstname','contact_number',
        'middlename','ext','gender','age','ext','birthdate','category','purok','street','barangay','city'])
        ->with('transaction')->find($id);

        return response()->json($patients);
    }

    public function assessment()
    {
        $patients = Patient::whereHas('transaction', function ($query) {
            $query->where('status', 'for assessment')
                ->whereDate('transaction_date', now()->toDateString());
        })
            ->with(['transaction' => function ($query) {
                $query->where('status', 'for assessment')
                    ->whereDate('transaction_date', now()->toDateString());
            }])
            ->get();

        return response()->json($patients);
    }

   


    public function storeAll(Request $request) // thiis method is for adding new patient, transaction and vitals
    {
        $userId = Auth::id();

        try {
            // ✅ Validate patient data
            $patientData = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'ext' => 'nullable|string|max:255',
                'birthdate' => 'required|date',
                'contact_number' => 'nullable|string|max:11',
                'age' => 'integer',
                'gender' => 'required|string|max:11',
                'is_not_tagum' => 'boolean',
                'street' => 'nullable|string|max:255',
                'purok'  => 'nullable|string|max:255',
                'barangay' => 'required|string|max:255',
                'city' => 'nullable|string|max:255',
                'province' => 'nullable|string|max:255',
                'category' => 'required|in:Child,Adult,Senior',
                'is_pwd' => 'boolean',
                'is_solo' => 'boolean',

            ]);

            // ✅ Check if patient already exists
            $existingPatient = Patient::select(['lastname', 'birthdate', 'firstname', 'lastname'])->where('firstname', $patientData['firstname'])
                ->where('lastname', $patientData['lastname'])
                ->where('birthdate', $patientData['birthdate']) // Better uniqueness check
                ->first();

            if ($existingPatient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient already has a record. Please add a new transaction instead.',
                    'patient' => $existingPatient
                ], 409); // 409 Conflict
            }

            // ✅ Add logged-in user ID to patient data
            $patientData['user_id'] = $userId;
            // ✅ Create new patient
            $patient = Patient::create($patientData,[
                'user_id' => $userId, // Ensure user_id is set
            ]);

            // ✅ Generate transaction number
            $datePart = now()->format('Y-m-d');
            $sequenceFormatted = str_pad($patient->id, 5, '0', STR_PAD_LEFT);
            $transactionNumber = "{$datePart}-{$sequenceFormatted}";

            // ✅ Validate transaction data
            $transactionData = $request->validate([
                'transaction_type' => 'required|string|max:255',
                'transaction_date' => 'required|string|max:255',
                'transaction_mode' => 'required|string|max:255',
                'purpose' => 'required|string|max:255',
            ]);

            $transactionData['patient_id'] = $patient->id;
            $transactionData['transaction_number'] = $transactionNumber;
            $transaction = Transaction::create($transactionData);

            // ✅ Validate vital signs
            $vitalData = $request->validate([
                'height' => 'required|string|max:255',
                'weight' => 'required|string|max:255',
                'bmi' => 'nullable|string|max:255',
                'temperature' => 'nullable|string|max:255',
                'waist' => 'nullable|string|max:255',
                'pulse_rate' => 'nullable|string|max:255',
                'sp02' => 'nullable|string|max:255',
                'heart_rate' => 'nullable|string|max:255',
                'blood_pressure' => 'nullable|string|max:255',
                'respiratory_rate' => 'nullable|string|max:255',
                'medicine' => 'nullable|string|max:255',
                'LMP' => 'nullable|string|max:255',
            ]);

            $vitalData['patient_id'] = $patient->id;
            $vitalData['transaction_id'] = $transaction->id;
            $vital = Vital::create($vitalData);

            return response()->json([
                'success' => true,
                'message' => 'Patient, transaction, and vitals created successfully.',
                'patient' => $patient,
                'transaction' => $transaction,
                'vital' => $vital,
                'transaction_number' => $transactionNumber
            ]);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'errors' => $qe->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }
    }


    public function update(PatientRequest $request, $id) // this method is for updating patient
    {
            $validated = $request->validated();

           $patient = Patient::findOrFail($id);
            // Update patient with validated data
            $patient->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Patient updated successfully',
                'patient' => $patient
            ]);

    }

    public function addTransactionAndVitals(AddTransactionRequest $request) // this is methiod for adding transaction and vitals for existing patient
    {
        try {
            // ✅ Validate all incoming data except existence check
            $validated = $request->validated();

            // ✅ Check if patient exists
            $patient = Patient::find($validated['patient_id']);
            if (!$patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient does not exist. Please add the patient first.',
                    'patient_id'=> $patient
                ], 404);
            }

            // ✅ Generate transaction number
            $datePart = now()->format('Y-m-d');
            $sequenceFormatted = str_pad($patient->id, 5, '0', STR_PAD_LEFT);
            $transactionNumber = "{$datePart}-{$sequenceFormatted}";

            // ✅ Create transaction
            $transaction = Transaction::create([
                'patient_id' => $patient->id,
                'transaction_number' => $transactionNumber,
                'transaction_type' => $validated['transaction_type'],
                'transaction_date' => $validated['transaction_date'],
                'transaction_mode' => $validated['transaction_mode'],
                'purpose' => $validated['purpose'],
            ]);

            // ✅ Create vitals
            $vital = Vital::create([
                'patient_id' => $patient->id,
                'transaction_id' => $transaction->id,
                'height' => $validated['height'],
                'weight' => $validated['weight'],
                'bmi' => $validated['bmi'] ?? null,
                'temperature' => $validated['temperature'] ?? null,
                'waist' => $validated['waist'] ?? null,
                'pulse_rate' => $validated['pulse_rate'] ?? null,
                'sp02' => $validated['sp02'] ?? null,
                'heart_rate' => $validated['heart_rate'] ?? null,
                'blood_pressure' => $validated['blood_pressure'] ?? null,
                'respiratory_rate' => $validated['respiratory_rate'] ?? null,
                'medicine' => $validated['medicine'] ?? null,
                'LMP' => $validated['LMP'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction and vitals added successfully for existing patient.',
                'patient' => $patient,
                'transaction' => $transaction,
                'vital' => $vital,
                'transaction_number' => $transactionNumber
            ]);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'errors' => $qe->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }
    }
}
