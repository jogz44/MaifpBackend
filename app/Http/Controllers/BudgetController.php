<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Budget;
use App\Models\Billing;
use Illuminate\Http\Request;
use App\Http\Requests\BudgetRequest;
use Illuminate\Auth\Events\Validated;
use App\Models\budget_additional_funds;
use App\Http\Requests\BudgetAdditionalFundsRequest;
use App\Models\GuaranteeLetter;

class BudgetController extends Controller
{
    //
    public function list_of_funded()
    {
        $budget = Billing::all();
        return response()->json($budget);
    }

    public function dashboardBudget()
    {
        $budgets = Budget::all();
        $totalFunds = $budgets->sum('funds');
        $totalUsed = GuaranteeLetter::sum('total_billing'); // already released funds
        $remainingFunds = $totalFunds - $totalUsed;

        return response()->json([
            'total_funds' => $totalFunds,
            'released_funds' => $totalUsed, //  added
            'remaining_funds' => $remainingFunds,

        ]);
    }

    public function index()
    {
        $budgets = Budget::all();

        // $totalFunds = $budgets->sum('funds');
        // $totalUsed = GuaranteeLetter::sum('total_amount'); // ✅ already released funds
        // $remainingFunds = $totalFunds - $totalUsed;

        return response()->json(
            // 'total_funds' => $totalFunds,
            // 'released_funds' => $totalUsed, // ✅ added
            // 'remaining_funds' => $remainingFunds,
            $budgets
        );
    }

    public function store(BudgetRequest $request)
    {

        $validated = $request->validated();
        $budget = Budget::create($validated);

        return response()->json([
            'message' => 'Budget Successfully saved',
            'Budget' => $budget,
        ]);
    }





}
