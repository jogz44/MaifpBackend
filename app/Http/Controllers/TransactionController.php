<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\vital;
use App\Models\Patient;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Representative;
use App\Http\Requests\VitalRequest;
use Illuminate\Database\QueryException;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\AddTransactionRequest;
use App\Http\Requests\RepresentativeRequest;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    // Add methods for handling transactions here
    // For example, you might have methods to create, update, delete, and fetch transactions

    public function assessment()
    {
        $patients = Patient::whereHas('transaction', function ($query) {
            $query->where('status','assessment')
                ->whereDate('transaction_date', now()->toDateString());
        })
            ->with(['transaction' => function ($query) {
                $query->where('status','assessment')
                    ->whereDate('transaction_date', now()->toDateString());
            }])
            ->get();

        return response()->json($patients);
    }

    
    public function index()
    {

    $transaction = Transaction::with('laboratories')->get();
    return response()->json($transaction);

     }

    public function show($id)
    {
        // Logic to fetch a transaction by ID
        $transaction = Transaction::with(['vital','laboratories','representative'])->find($id);

        return response()->json($transaction);

    }

    public function rep_update(RepresentativeRequest $request, $id) // this function is for the updating or edit the Representative
    {
        // Logic to update a transaction
        $validated =  $request->validated();

        $transaction = Representative::findOrFail($id);
        $transaction->update($validated);

        return response()->json([
            'message' => 'Representative updated successfully.',
            'transaction' => $transaction
        ]);
    }

    // this method is for updating transaction
    public function update(TransactionRequest $request, $id)
    {
        // Logic to update a transaction
        $validated =  $request->validated();

        $transaction = Transaction::findOrFail($id);
        $transaction->update($validated);

        return response()->json([
            'message' => 'Transaction updated successfully.',
            'transaction' => $transaction]);
    }

    // this method is for updating vital signs
    public function vital_update(VitalRequest $request, $id)
    {
        // Logic to update a transaction
        $validated =  $request->validated();

        $vital = vital::findOrFail($id);
        $vital->update($validated);

        return response()->json([
            'message' => 'vital updated successfully.',
            'vital' => $vital
        ]);
    }

    // this method is for updating transaction status if the patient are qualified or unqualified
    public function status_update(Request $request, $id)
    {
        // Logic to update a transaction
        $validated =  $request->validate([
            'status' => 'sometimes|required|string|max:255',
        ]);
        $transaction = Transaction::findOrFail($id);
        $transaction->update($validated);

        return response()->json([
            'message' => 'Transaction updated status successfully.',
            'transaction' => $transaction
        ]);
    }

    //deleting the AllTransactions Data
    public function deleteAllTransactions()
    {
        try {
            Patient::query()->delete();

            return response()->json([
                'success' => true,
                'message' => 'All transactions deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transactions.',
                'error' => $th->getMessage()
            ], 500);
        }
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
                    'patient_id' => $patient
                ], 404);
            }

            // ✅ Generate transaction number
            $datePart = now()->format('Y-m-d');
            $sequenceFormatted = str_pad($patient->id, 5, '0', STR_PAD_LEFT);
            $transactionNumber = "{$datePart}-{$sequenceFormatted}";

            $representative = Representative::create([
                'rep_name' => $validated['rep_name'],
                'rep_relationship' => $validated['rep_relationship'] ?? 'NA',
                'rep_contact' => $validated['rep_contact'] ?? 'NA',
                'rep_barangay' => $validated['rep_barangay'] ?? 'NA',
                'rep_address' => $validated['rep_address'] ?? 'NA',
                'rep_purok' => $validated['rep_purok'] ?? 'NA',
                'rep_street' => $validated['rep_street'] ?? 'NA',
                'rep_province' => $validated['rep_province'] ?? 'NA',
                'rep_city' => $validated['rep_city'] ?? 'NA',
            ]);

            // ✅ Create transaction
            $transaction = Transaction::create([
                'patient_id' => $patient->id,
                'representative_id' => $representative->id,
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
                'bmi' => $validated['bmi'] ?? 'NA',
                'temperature' => $validated['temperature'] ?? 'NA',
                'waist' => $validated['waist'] ?? 'NA',
                'pulse_rate' => $validated['pulse_rate'] ?? 'NA',
                'sp02' => $validated['sp02'] ??  'NA',
                'heart_rate' => $validated['heart_rate'] ?? 'NA',
                'blood_pressure' => $validated['blood_pressure'] ?? 'NA',
                'respiratory_rate' => $validated['respiratory_rate'] ?? 'NA',
                'medicine' => $validated['medicine'] ?? 'NA',
                'LMP' => $validated['LMP'] ?? 'NA',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction and vitals added successfully for existing patient.',
                'patient' => $patient,
                'transaction' => $transaction,
                'vital' => $vital,
                'representative' => $representative,
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
