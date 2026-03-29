<?php

namespace App\Http\Controllers;

use App\Events\BadgeUpdated;
use App\Http\Requests\AddTransactionRequest;
use App\Http\Requests\RepresentativeRequest;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\VitalRequest;
use App\Models\Patient;
use App\Models\Representative;
use App\Models\Transaction;
use App\Models\vital;
use App\Services\BadgeService;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller

{

    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    // Add methods for handling transactions here
    // For example, you might have methods to create, update, delete, and fetch transactions

    public function index()
    {

        $transaction = Transaction::with('laboratories')->get();
        return response()->json($transaction);
    }

    public function show($id, Request $request)
    {
        $user = Auth::user();
        $transaction = Transaction::with([
            'vital',
            'representative',
            'patient',
            'mammogram_details',
            'medicationDetails',
            'examination_details',
            'radiologies_details',
            'ultrasound_details'
        ])
            ->findOrFail($id);

        // ✅ Get patient name if linked
        $patientName = $transaction->patient
            ? trim("{$transaction->patient->firstname} {$transaction->patient->middlename} {$transaction->patient->lastname} {$transaction->patient->ext}")
            : 'Unknown Patient';

        // ✅ Actor name
        $actorName = $user ? "{$user->first_name} {$user->last_name}" : 'System';

        // ✅ Log activity
        activity($actorName)
            ->causedBy($user)
            ->performedOn($transaction)
            ->withProperties([
                'ip'   => $request->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
            ])
            ->log("Viewed transaction record (ID: {$transaction->id}) for Patient: {$patientName}");

        return response()->json($transaction);
    }


    public function rep_update(RepresentativeRequest $request, $id) // this function is for the updating or edit the Representative
    {
        $user = Auth::user(); // Get the authenticated user
        // Logic to update a transaction
        $validated =  $request->validated();

        $rep = Representative::findOrFail($id);

        $oldData = $rep->toArray(); // 👈 old values before update
        $rep->update($validated);
        $newData = $rep->toArray(); // 👈 new values after update

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($rep)
            ->withProperties([
                'ip'      => $request->ip(),
                'date'    => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'old'     => $oldData,
                'new'     => $newData,
                'changes' => $validated,
            ])
            ->log("Updated Representative [{$rep->rep_name}]");

        return response()->json([
            'message' => 'Representative updated successfully.',
            'representative' => $rep
        ]);
    }

    // this method is for updating transaction
    public function update(TransactionRequest $request, $id)
    {
        $user = Auth::user(); // Get the authenticated user
        // Logic to update a transaction
        $validated =  $request->validated();

        $transaction = Transaction::findOrFail($id);

        $oldData = $transaction->toArray();
        $transaction->update($validated);
        $newData = $transaction->toArray();

        // ✅ Then broadcast the fresh counts AFTER the DB has changed
        $counts = app(BadgeService::class)->getBadgeCounts();
        broadcast(new BadgeUpdated($counts));

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($transaction)
            ->withProperties([
                'ip'      => $request->ip(),
                'date'    => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'old'     => $oldData,
                'new'     => $newData,
                'changes' => $validated,
            ])
            ->log("Updated Transaction ID: {$transaction->id}");


        return response()->json([
            'message' => 'Transaction updated successfully.',
            'transaction' => $transaction
        ]);
    }

    // // this method is for updating vital signs

    public function vital_update(VitalRequest $request, $id)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $vital = Vital::with('patient')->findOrFail($id); // 👈 load patient

        $oldData = $vital->toArray();
        $vital->update($validated);
        $newData = $vital->fresh()->toArray(); // 👈 refresh after update

        $patientName = $vital->patient
            ? $vital->patient->firstname . ' ' . $vital->patient->lastname
            : 'Unknown Patient';

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($vital)
            ->withProperties([
                'ip'      => $request->ip(),
                'date'    => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'old'     => $oldData,
                'new'     => $newData,
                'changes' => $validated,
            ])
            ->log("Updated Vital Signs for Patient {$patientName}");

        return response()->json([
            'message' => 'Vital updated successfully.',
            'vital'   => $vital
        ]);
    }

    // this method is for updating transaction status if the patient are qualified or unqualified
    public function updateTransaction(Request $request, $id)
    {
        //args: id = transaction_id

        // Logic to update a transaction
        $validated =  $request->validate([
            'status' => 'required|string'
        ]);


        $result = $this->transactionService->update($validated,$id,$request);

        // ✅ Then broadcast the fresh counts AFTER the DB has changed
        $counts = app(BadgeService::class)->getBadgeCounts();
        broadcast(new BadgeUpdated($counts));

        return $result;


    }

    //deleting the AllTransactions Data
    public function deleteAllTransactions(Request $request)
    {

        $user = Auth::user();

        try {
            Patient::query()->delete();

            activity($user->first_name . ' ' . $user->last_name)
                ->causedBy($user)
                ->withProperties([
                    'ip'   => $request->ip(),
                    'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                ])
                ->log("Deleted ALL Transactions & Patients");

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

    // add transaction  and vitals
    public function addTransactionAndVitals(AddTransactionRequest $request)
    {
        try {
            // ✅ Validate all incoming data
            $validated = $request->validated();

            // ✅ Check if patient exists
            $patient = Patient::find($validated['patient_id']);
            if (!$patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient does not exist. Please add the patient first.',
                ], 404);
            }

            // ✅ Generate transaction number
            // $datePart = now()->format('Y-m-d');
            // $sequenceFormatted = str_pad($patient->id, 5, '0', STR_PAD_LEFT);
            // $transactionNumber = "{$datePart}-{$sequenceFormatted}";

            // ✅ Generate transaction number (duplicate-safe)
            $transactionNumber = \Illuminate\Support\Facades\DB::transaction(function () use ($patient) {
                $datePart = now()->format('Y-m-d');
                $patientPadded = str_pad($patient->id, 4, '0', STR_PAD_LEFT); // 0615

                // Count how many transactions this patient already has today
                $todayCount = Transaction::where('patient_id', $patient->id)
                    ->whereDate('created_at', today())
                    ->lockForUpdate()
                    ->count();

                $sequence = $todayCount + 1; // 1, 2, 3...

                return "{$datePart}-{$patientPadded}{$sequence}";
                // e.g. 2026-03-25-06151
            });

            // ✅ Create representative
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

            // ✅ Determine assistance based on patient's PhilHealth ID

            // Before the can add transaction here check first if he have transaction_date exisitng today it must 1 transaction per day

            // // Check if patient already has transaction today
            // $hasTransactionToday = Transaction::where('patient_id', $patient->id)
            //     ->whereDate('transaction_date', Carbon::today())
            //     ->exists();

            // if ($hasTransactionToday) {
            //     return response()->json([
            //         'message' => 'Patient already has a transaction today.'
            //     ], 400);
            // }


            // ✅ Create transaction
            $transaction = Transaction::create([
                'patient_id' => $patient->id,
                'representative_id' => $representative->id,
                'transaction_number' => $transactionNumber,
                'transaction_type' => $validated['transaction_type'],
                'transaction_date' => $validated['transaction_date'],
                'transaction_mode' => $validated['transaction_mode'],
                'status' => $validated['status'],
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
                'sp02' => $validated['sp02'] ?? 'NA',
                'heart_rate' => $validated['heart_rate'] ?? 'NA',
                'blood_pressure' => $validated['blood_pressure'] ?? 'NA',
                'respiratory_rate' => $validated['respiratory_rate'] ?? 'NA',
                'medicine' => $validated['medicine'] ?? 'NA',
                'LMP' => $validated['LMP'] ?? 'NA',
            ]);

            // ✅ Add activity log
            $user = Auth::user();
            $actorName = $user ? $user->first_name . ' ' . $user->last_name : 'System';
            $patientName = $patient->firstname . ' ' . $patient->lastname;


            // ✅ Then broadcast the fresh counts AFTER the DB has changed
            $counts = app(BadgeService::class)->getBadgeCounts();
            broadcast(new BadgeUpdated($counts));


            activity($actorName)
                ->causedBy($user)
                ->performedOn($transaction)
                ->withProperties([
                    'ip' => $request->ip(),
                    'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                    'transaction' => $transaction->toArray(),
                    'vital' => $vital->toArray(),
                    'representative' => $representative->toArray(),
                ])
                ->log("Created new transaction {$transaction->transaction_number} and vitals for Patient: {$patientName}");

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

    // this method is for updating transaction status if the patient want to avail the maifip so his status will be change to assessment
    public function status_to_maifip(Request $request, $TransactionId)
    {
        $user = Auth::user();
        // Logic to update a transaction
        $validated =  $request->validate([
            'status' => 'required|string|in:evaluation',
            'maifip' => 'required|boolean',
        ]);
        $transaction = Transaction::with('patient')->findOrFail($TransactionId);



        $oldData = $transaction->toArray();
        $transaction->update($validated);


        // ✅ Then broadcast the fresh counts AFTER the DB has changed
        $counts = app(BadgeService::class)->getBadgeCounts();
        broadcast(new BadgeUpdated($counts));

        $newData = $transaction->toArray();

        // ✅ Get patient name
        $patientName = $transaction->patient
            ? $transaction->patient->firstname . ' ' . $transaction->patient->lastname
            : 'Unknown Patient';

        // ✅ Log activity with patient name
        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($transaction)
            ->withProperties([
                'ip'      => $request->ip(),
                'date'    => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'old'     => $oldData,
                'new'     => $newData,
                'changes' => $validated,
            ])
            ->log("Updated patient transaction status to {$validated['status']} for Patient: {$patientName}");

        return response()->json([
            'message' => 'Transaction updated status successfully for to maifip.',
            'transaction' => $transaction
        ]);
    }
}
