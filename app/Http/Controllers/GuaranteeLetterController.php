<?php

namespace App\Http\Controllers;


use App\Models\Budget;
use App\Models\Patient;
use App\Models\Assistances;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\GuaranteeLetter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\vw_guarantee_patients;
use App\Http\Requests\GuaranteeLetterRequest;

class GuaranteeLetterController extends Controller
{
    // fetching the patient have complete status on the transaction this method is on the guarantee letter fetch the patient
    // public function index()
    // {
    //     $patients = Patient::whereHas('transaction', function ($query) {
    //         $query
    //         // ->whereDate('transaction_date', Carbon::today())
    //             ->where('status', 'Complete');
    //     })
    //         ->whereDoesntHave('transaction.guaranteeLetter', function ($query) {
    //             $query->where('status', 'Funded');
    //         })
    //         ->with([
    //             'transaction' => function ($q) {
    //                 $q
    //                 // ->whereDate('transaction_date', Carbon::today())
    //                     ->where('status', 'Complete');
    //             }
    //         ])
    //         ->get([
    //             'id',
    //             'firstname',
    //             'lastname',
    //             'middlename',
    //             'ext',
    //             'birthdate',
    //             'age',
    //             'contact_number',
    //             'barangay'
    //         ]);

    //     return response()->json($patients);



    // }

    // public function index() //
    // {
    //     $patients = Assistances::with('patient:id,firstname,lastname,middlename,ext,birthdate,contact_number,barangay',
    //     'transaction:id,transaction_type',
    //     )->get();

    //     return response()->json($patients);
    // }

    public function guaranteeLetter($transactionId)
    {
        $transaction = Assistances::with([
            'Funds',
            'transaction',
            'patient' => function ($query) {
                $query->select(
                    'id',
                    'lastname',
                    'firstname',
                    'gender',
                    'age',
                    'middlename',
                    'birthdate',
                    'purok',
                    'street',
                    'barangay',
                    'city',
                    'province'
                );
            }
        ])
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        // Decode JSON fields
        $transaction->laboratories_details = json_decode($transaction->laboratories_details, true);
        $transaction->medication = json_decode($transaction->medication, true);

        // ✅ Combine purok, street, and barangay into address
        if ($transaction->patient) {
            $transaction->patient->address = trim(
                collect([
                    $transaction->patient->purok,
                    $transaction->patient->street,
                    $transaction->patient->barangay,
                    $transaction->patient->city,
                    $transaction->patient->province,
                ])->filter()->implode(', ')
            );

            // Optionally hide the original fields
            unset($transaction->patient->purok);
            unset($transaction->patient->street);
            unset($transaction->patient->barangay);
            unset($transaction->patient->city);
            unset($transaction->patient->province);
        }

        return response()->json($transaction);
    }



    public function index()
    {
        $transactions = Transaction::where('status', 'Complete')
            ->with(['patient' => function ($query) {
                // Only fetch the patient fields you need
                $query->select(
                    'id', // important! primary key for relationship
                    'firstname',
                    'lastname',
                    'middlename',
                    'ext',
                    'birthdate',
                    'age',
                    'contact_number',
                    'barangay'
                );
            }])
            ->select([
                DB::raw('id as transaction_id'), // alias properly
                'patient_id',
                'transaction_type',
                'transaction_date',
                'status'
            ])
            ->get();

        return response()->json($transactions);
    }

    // public function index()
    // {
    //     $patients = Patient::whereHas('transaction', function ($query) {
    //         $query
    //             // ->whereDate('transaction_date', Carbon::today())
    //             ->where('status', 'Complete');
    //     })
    //         ->whereDoesntHave('transaction.guaranteeLetter', function ($query) {
    //             $query->where('status', 'Funded');
    //         })
    //         ->with([
    //             'transaction' => function ($q) {
    //                 $q
    //                     // ->whereDate('transaction_date', Carbon::today())
    //                     ->where('status', 'Complete');
    //             }
    //         ])
    //         ->get([
    //             'id',
    //             'firstname',
    //             'lastname',
    //             'middlename',
    //             'ext',
    //             'birthdate',
    //             'age',
    //             'contact_number',
    //             'barangay'
    //         ]);

    //     return response()->json($patients);
    // }


    // store the guarantee letter then deduc the total_billing of patient on the remainings funds on the budget
    // public function store(GuaranteeLetterRequest $request)
    // {
    //     $user = Auth::user();
    //     $validated = $request->validated();

    //     // Get total funds from all budgets
    //     $totalFunds = Budget::sum('funds');

    //     // Get total used so far (sum of billings)
    //     $totalUsed = GuaranteeLetter::sum('total_billing');

    //     // Remaining funds
    //     $remainingFunds = $totalFunds - $totalUsed;

    //     // Check if enough funds are available
    //     if ($remainingFunds < $validated['total_billing']) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => "Not enough funds. Please add more funds before creating this billing. Remaining funds: {$remainingFunds}",
    //         ], 400);
    //     }

    //     // Create the billing record
    //     $billing = GuaranteeLetter::create($validated);

    //     // Fetch patient & transaction details
    //     $transaction = Transaction::with('patient')->find($billing->transaction_id);

    //     // Update remaining funds after this billing
    //     $remainingFunds -= $validated['total_billing'];

    //     // ✅ Activity log
    //     if ($transaction && $transaction->patient) {
    //         $patientName = $transaction->patient->firstname . ' ' . $transaction->patient->lastname;

    //         activity($user->first_name . ' ' . $user->last_name)
    //             ->causedBy($user)
    //             ->performedOn($billing)
    //             ->withProperties([
    //                 'ip'              => $request->ip(),
    //                 'date'            => now('Asia/Manila')->format('Y-m-d h:i:s A'),
    //                 'transaction_id'  => $transaction->id,
    //                 'patient_id'      => $transaction->patient->id,
    //                 'patient_name'    => $patientName,
    //                 'billing_amount'  => $billing->total_billing,
    //                 'remaining_funds' => $remainingFunds,
    //             ])
    //             ->log("Successfully Deduc Guarantee Letter for Patient {$patientName} (Transaction ID: {$transaction->id}) with Billing Amount: {$billing->total_billing}");
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Billing created successfully',
    //         'billing' => $billing,
    //         'total_funds' => $totalFunds,
    //         'remaining_funds' => $remainingFunds
    //     ]);
    // }




    public function update(Request $request, $transaction_id)
    {
        // ✅ Validation
        $validated = $request->validate([
            'gl_number'   => 'required|string|unique:assistances,gl_number,' . $transaction_id . ',transaction_id',
            'fund_source' => 'required|string',
            'fund_amount' => 'required|numeric',
        ]);

        // ✅ Find Assistance record by transaction_id
        $assistance = Assistances::where('transaction_id', $transaction_id)->first();

        if (!$assistance) {
            return response()->json([
                'message' => "No Assistance record found for transaction_id {$transaction_id}"
            ], 404);
        }

        // ✅ Update gl_number (already validated as unique)
        $assistance->update([
            'gl_number' => $validated['gl_number'],
        ]);

        // ✅ Add fund source
        $assistance->funds()->create([
            'fund_source' => $validated['fund_source'],
            'fund_amount' => $validated['fund_amount'],
        ]);

        return response()->json([
            'message'    => 'Successfully updated assistance and added fund source',
            'assistance' => $assistance->load('funds'),
        ]);
    }



    public function update_status(Request $request, $transaction_id)
    {
        // ✅ Validation
        $validated = $request->validate([
           'status' => 'required|in:Funded'
        ]);

        // ✅ Find Assistance record by transaction_id
        $transaction = Transaction::where('id', $transaction_id)->first();

        if (!$transaction) {
            return response()->json([
                'message' => "No Assistance record found for transaction_id {$transaction_id}"
            ], 404);
        }

        // ✅ Update gl_number (already validated as unique)
        $transaction->update([
            'status' => $validated['status'],
        ]);


        return response()->json([
            'message'    => 'Successfully updated status',
            'transaction' => $transaction,
        ]);
    }
}
