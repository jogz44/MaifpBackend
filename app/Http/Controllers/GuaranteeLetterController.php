<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Budget;
use App\Models\Billing;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\GuaranteeLetter;
use App\Http\Requests\BillingRequest;
use App\Http\Requests\GuaranteeLetterRequest;

class GuaranteeLetterController extends Controller
{
    //

    // public function index()
    // {
    //     $patients = Patient::whereHas('transaction', function ($query) {
    //         $query->whereDate('transaction_date', Carbon::today())
    //             ->where('status', 'Complete');
    //     })
    //         ->with([

    //             'transaction' => function ($q) {
    //                 $q->whereDate('transaction_date', Carbon::today())
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


    public function index()
    {
        $patients = Patient::whereHas('transaction', function ($query) {
            $query->whereDate('transaction_date', Carbon::today())
                ->where('status', 'Complete');
        })
            ->whereDoesntHave('transaction.guaranteeLetter', function ($query) {
                $query->where('status', 'Funded');
            })
            ->with([
                'transaction' => function ($q) {
                    $q->whereDate('transaction_date', Carbon::today())
                        ->where('status', 'Complete');
                }
            ])
            ->get([
                'id',
                'firstname',
                'lastname',
                'middlename',
                'ext',
                'birthdate',
                'age',
                'contact_number',
                'barangay'
            ]);

        return response()->json($patients);
    }


    // public function store (GuaranteeLetterRequest $request){ // store for guarantee letter

    //     $validated = $request->validated();

    //     $guarantee = GuaranteeLetter::updateOrCreate(
    //         ['transaction_id' => $validated['transaction_id']], // match condition
    //         $validated                                          // values to update
    //     );
    //     return response()->json($guarantee);

    // }

    public function store(GuaranteeLetterRequest $request)
    {
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

                // 'total_funds' => $totalFunds,
                // 'remaining_funds' => $remainingFunds,
            ], 400); // Bad Request
        }

        // Create the billing record
        $billing = GuaranteeLetter::create($validated);

        // Update remaining funds after this billing
        $remainingFunds -= $validated['total_billing'];

        return response()->json([
            'success' => true,
            'message' => 'Billing created successfully',
            'billing' => $billing,
            'total_funds' => $totalFunds,
            'remaining_funds' => $remainingFunds
        ]);
    }
}
