<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
use App\Http\Requests\BudgetRequest;

class BudgetController extends Controller
{
    //


    public function index()
    {
        $budget = Budget::all();
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

        $budget = Budget::created($validated);

        return response()->json([
            'message' => 'Budget Successfully saved',
            'Budget' => $budget,
        ]);
    }
}
