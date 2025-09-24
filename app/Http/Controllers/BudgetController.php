<?php

namespace App\Http\Controllers;


use App\Models\Budget;
use App\Models\Billing;
use App\Models\GuaranteeLetter;
use App\Http\Requests\BudgetRequest;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    // list of funds
    // public function index()
    // {
    //     $budgets = Budget::all();
    //     return response()->json(

    //         $budgets
    //     );
    // }

    // // fetching the total funds, total used, and remaining funds
    // public function dashboardBudget()
    // {
    //     $budgets = Budget::all();
    //     $totalFunds = $budgets->sum('funds');
    //     $totalUsed = GuaranteeLetter::sum('total_billing'); // already released funds
    //     $remainingFunds = $totalFunds - $totalUsed;

    //     return response()->json([
    //         'total_funds' => $totalFunds,
    //         'released_funds' => $totalUsed, //  added
    //         'remaining_funds' => $remainingFunds,

    //     ]);
    // }

    // // store budget funds
    // public function store(BudgetRequest $request)
    // {
    //     $user = Auth::user();
    //     // Validate request
    //     $validated = $request->validated();
    //     // Create budget
    //     $budget = Budget::create($validated);
    //     // Activity log
    //     activity($user->first_name . ' ' . $user->last_name)
    //         ->causedBy($user)
    //         ->performedOn($budget)
    //         ->withProperties([
    //             'ip'   => $request->ip(),
    //             'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
    //             'new'  => $budget->toArray(),
    //         ])
    //         ->log("Added a new Budget Funds with ID: {$budget->id} and Amount: {$budget->funds}");

    //     return response()->json([
    //         'message' => 'Budget Successfully saved',
    //         'Budget'  => $budget,
    //     ]);
    // }
}
