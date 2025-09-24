<?php

namespace App\Http\Controllers;


use App\Models\Budget;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\GuaranteeLetter;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\GuaranteeLetterRequest;
use App\Models\Assistances;
use App\Models\vw_guarantee_patients;

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

    public function index() //
    {
        $patients = Assistances::with('patient:id,firstname,lastname,middlename,ext,birthdate,contact_number,barangay',
        'transaction:id,transaction_type',
        )->get();

        return response()->json($patients);
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
    public function store(GuaranteeLetterRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Get total funds from all budgets
        $totalFunds = Budget::sum('funds');

        // Get total used so far (sum of billings)
        $totalUsed = GuaranteeLetter::sum('total_billing');

        // Remaining funds
        $remainingFunds = $totalFunds - $totalUsed;

        // Check if enough funds are available
        if ($remainingFunds < $validated['total_billing']) {
            return response()->json([
                'success' => false,
                'message' => "Not enough funds. Please add more funds before creating this billing. Remaining funds: {$remainingFunds}",
            ], 400);
        }

        // Create the billing record
        $billing = GuaranteeLetter::create($validated);

        // Fetch patient & transaction details
        $transaction = Transaction::with('patient')->find($billing->transaction_id);

        // Update remaining funds after this billing
        $remainingFunds -= $validated['total_billing'];

        // ✅ Activity log
        if ($transaction && $transaction->patient) {
            $patientName = $transaction->patient->firstname . ' ' . $transaction->patient->lastname;

            activity($user->first_name . ' ' . $user->last_name)
                ->causedBy($user)
                ->performedOn($billing)
                ->withProperties([
                    'ip'              => $request->ip(),
                    'date'            => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                    'transaction_id'  => $transaction->id,
                    'patient_id'      => $transaction->patient->id,
                    'patient_name'    => $patientName,
                    'billing_amount'  => $billing->total_billing,
                    'remaining_funds' => $remainingFunds,
                ])
                ->log("Successfully Deduc Guarantee Letter for Patient {$patientName} (Transaction ID: {$transaction->id}) with Billing Amount: {$billing->total_billing}");
        }

        return response()->json([
            'success' => true,
            'message' => 'Billing created successfully',
            'billing' => $billing,
            'total_funds' => $totalFunds,
            'remaining_funds' => $remainingFunds
        ]);
    }
}
