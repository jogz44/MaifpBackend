<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionTypeRequest;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class TransactionTypeController extends Controller
{
    //

    public function index() // for fetching all transaction types
    {
        // Logic to fetch all transaction types
        $transactionTypes = TransactionType::all();
        return response()->json($transactionTypes);
    }

    public function show($id) // for  showing a transaction by ID
    {

        $transactionTypes = TransactionType::findOrFail($id);
        return response()->json([
            'transaction_type' => $transactionTypes
        ]);
    }


    public function store(TransactionTypeRequest $request) // for creating transaction type
    {

        $validated = $request->validated();

        $transactionTypes = TransactionType::select(['id','transaction_name','status'])
        ->where('transaction_name', $validated['transaction_name'])->first();

        // error
        if ($transactionTypes){
            return response()->json([
                'message' => 'The name is already Existing please check the record',
                'transaction' => $transactionTypes,
            ], 409);

        }

       $transactionTypes = TransactionType::create($validated);

        return response()->json([
            'message' => 'Transaction Type created successfully.',
            'transacation_type' => $transactionTypes

        ], 201);
    }


     public function update(TransactionTypeRequest $request, $id) // for updating transaction type
     {

        $validated = $request->validated();

        $transactionTypes = TransactionType::findOrfail($id);
        $transactionTypes->update($validated);

        return response()->json([
            'message' => 'Transaction Type updated successfully.',
            'transaction_type' => $transactionTypes
        ]);
     }

     public function destroy($id)
     {

        $transactionTypes = TransactionType::findOrFail($id);

        $transactionTypes->delete();

        return response()->json([
            'message' => 'Transaction Type deleted successfully.',
        ]);
     }

}
