<?php

namespace App\Http\Controllers;

use App\Models\vital;
use App\Models\Patient;

use App\Models\Transaction;
use Illuminate\Http\Request;

use Illuminate\Database\QueryException;

use Illuminate\Validation\ValidationException;

class PatientController extends Controller
{
    //

    // public function store(PatientRequest $request)
    // {
    //     try {
    //         $validated = $request->validated();

    //         // Save patient
    //         $patient = Patient::create($validated);

    //         // Generate transaction number: year-month-day-patient_id
    //         $datePart = now()->format('Y-m-d'); // Example: 2025-05-10

    //         // Use patient id as sequence
    //         $sequenceFormatted = str_pad($patient->id, 5, '0', STR_PAD_LEFT);

    //         $transactionNumber = "{$datePart}-{$sequenceFormatted}";

    //         return response()->json([
    //             'success' => true,
    //             'patient' => $patient,
    //             'transaction_number' => $transactionNumber
    //         ]);
    //     } catch (ValidationException $ve) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation error',
    //             'errors' => $ve->errors()
    //         ], 422);
    //     } catch (QueryException $qe) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Database error',
    //             'errors' => $qe->getMessage()
    //         ], 500);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An unexpected error occurred',
    //             'errors' => $th->getMessage()
    //         ], 500);
    //     }
    // }

    // public function transaction_store(TransactionRequest $request){


    //          $validated = $request->validated();
    //          $transaction = Transaction::create($validated);

    //          return response()->json([
    //              'success' => true,
    //              'transaction' => $transaction
    //          ]);

    // }

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


    // store the patient, transaction and vital signs
    public function storeAll(Request $request)
    {
        try {
            // Validate patient data
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
                // 'user_id' => 'required|exists:users,id'
                // Add other patient fields as needed
            ]);

            // Try to find existing patient by first and last name
            $patient = Patient::where('firstname', $patientData['firstname'])
                ->where('lastname', $patientData['lastname'])
                ->first();

            // If not found, create a new patient
            if (!$patient) {
                $patient = Patient::create($patientData);
            }

            // Generate transaction number
            $datePart = now()->format('Y-m-d');
            $sequenceFormatted = str_pad($patient->id, 5, '0', STR_PAD_LEFT);
            $transactionNumber = "{$datePart}-{$sequenceFormatted}";

            // Validate transaction data
            $transactionData = $request->validate([
                'transaction_type' => 'required|string|max:255',
                'transaction_date' => 'required|string|max:255',
                'transaction_mode' => 'required|string|max:255',
                'purpose' => 'required|string|max:255',

                // Add other transaction fields as needed
            ]);

            // Add patient_id and transaction_number to transaction data
            $transactionData['patient_id'] = $patient->id;
            $transactionData['transaction_number'] = $transactionNumber;

            // Save transaction
            $transaction = Transaction::create($transactionData);

            $vitalData = $request->validate([
                'height' => 'required|string|max:255',
                'weight' => 'required|string|max:255',
                'bmi' => 'required|nullable|string|max:255',
                'temperature' => 'required|nullable|string|max:255',
                // 'bmi' => 'required|nullable|string|max:255',
                'waist' => 'required|nullable|string|max:255',
                'pulse_rate' => 'required|nullable|string|max:255',
                'temperature' => 'required|nullable|string|max:255',
                'sp02' => 'required|nullable|string|max:255',
                'heart_rate' => 'required|nullable|string|max:255',
                'blood_pressure' => 'required|nullable|string|max:255',
                'respiratory_rate' => 'required|nullable|string|max:255',
                'medicine' => 'required|nullable|string|max:255',
                'LMP' => 'required|nullable|string|max:255',
            ]);

            $vitalData['patient_id'] = $patient->id;
            $vitalData['transaction_id'] = $transaction->id;

             $vital = vital::create($vitalData);

            return response()->json([
                'success' => true,
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


    public function update(Request $request, $id)
    {

            $patient = Patient::findOrFail($id);
            $validated = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'ext' => 'nullable|string|max:255',
                'contact_number' => 'nullable|string|max:11',
                'age' => 'integer',
            ]);

            // Update patient with validated data
            $patient->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Patient updated successfully',
                'patient' => $patient
            ]);

    }
    // public function storeAll(Request $request)
    // {
    //     try {
    //         // Validate patient data
    //         $patientData = $request->validate([
    //             'firstname' => 'required|string|max:255',
    //             'lastname' => 'required|string|max:255',
    //             // Add other patient fields as needed
    //         ]);

    //         // Save patient
    //         $patient = Patient::create($patientData);

    //         // Generate transaction number
    //         $datePart = now()->format('Y-m-d');
    //         $sequenceFormatted = str_pad($patient->id, 5, '0', STR_PAD_LEFT);
    //         $transactionNumber = "{$datePart}-{$sequenceFormatted}";

    //         // Validate transaction data
    //         $transactionData = $request->validate([
    //             'transaction_type' => 'required|string|max:255',
    //             // Add other transaction fields as needed
    //         ]);

    //         // Add patient_id and transaction_number to transaction data
    //         $transactionData['patient_id'] = $patient->id;
    //         $transactionData['transaction_number'] = $transactionNumber;

    //         // Save transaction
    //         $transaction = Transaction::create($transactionData);

    //         // Return both patient and transaction info
    //         return response()->json([
    //             'success' => true,
    //             'patient' => $patient,
    //             'transaction' => $transaction,
    //             'transaction_number' => $transactionNumber
    //         ]);
    //     } catch (ValidationException $ve) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation error',
    //             'errors' => $ve->errors()
    //         ], 422);
    //     } catch (QueryException $qe) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Database error',
    //             'errors' => $qe->getMessage()
    //         ], 500);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An unexpected error occurred',
    //             'errors' => $th->getMessage()
    //         ], 500);
    //     }
    // }

}
