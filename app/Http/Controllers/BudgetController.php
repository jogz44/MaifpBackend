<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetAdditionalFundsRequest;
use App\Models\Budget;
use Illuminate\Http\Request;
use App\Http\Requests\BudgetRequest;
use App\Models\budget_additional_funds;
use Exception;
use Illuminate\Auth\Events\Validated;

class BudgetController extends Controller
{
    //


    public function index()
    {
        $budget = Budget::with('releases')->get();
        return response()->json($budget);
    }


    public function show($id)
    {

        $budget = Budget::findOrfail($id);
        return response()->json([
            'budget' => $budget
        ]);
    }


    public function store(BudgetRequest $request)
    {

        $validated = $request->validated();
         $validated['remaining_funds'] =  $validated['funds'];

        $budget = Budget::create($validated);

        return response()->json([
            'message' => 'Budget Successfully saved',
            'Budget' => $budget,
        ]);
    }

    public function additionalFunds(BudgetAdditionalFundsRequest $request)
    {
        $validated = $request->validated();
        $additional = budget_additional_funds::create($validated);

        return response()->json([
            'message' => 'Successfully updated funds',
            'additional' => $additional
        ]);
    }


    public function releaseFunds(Request $request, $id)
    {

        $budget = Budget::findOrFail($id);

        try {
            $budget->releaseFunds(
                $request->release_amount,
            );

            return response()->json([
                'message' => 'Funds released successfully',
                'remaining_funds' => $budget->remaining_funds,
                'history' => $budget->releases
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

}
