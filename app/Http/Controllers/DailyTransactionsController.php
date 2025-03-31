<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Models\Daily_transactions as Transactions;

class DailyTransactionsController extends Controller
{
    //

    public function newTransactionID($id)
    {
        $dateNow = now()->format('Ymd');  // Get date as YYYYMMDD
        $customerId = $id;

        // Get the highest transaction_number for this customer
        $lastTransaction = Transactions::where('customer_id', $id)
            ->max('transaction_id');  // Get the highest transaction_number

        // Extract the last numeric part and increment
        $nextTransactionNumber = $lastTransaction ? (intval($lastTransaction) + 1) : 1;

        // Ensure numbering is 6-digit padded (e.g., 000001)
        $transactionNumber = str_pad($nextTransactionNumber, 6, '0', STR_PAD_LEFT);

        return $dateNow . '-' . $customerId . '-' . $transactionNumber;
    }



    public function index()
    {
        try {

            $transactions = Transactions::orderBy('id', 'desc')
                ->get();
            return response()->json(['success' => true, 'transactions' =>  $transactions]);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
            //throw $th;
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {

        try {

            $transactions = Transactions::where('id',$id)
                ->get();
            return response()->json(['success' => true, 'transactions' =>  $transactions]);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
            //throw $th;
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {

        try {

            $validationInput = $request->validate(
                [
                    'item_id' => 'required|exists:tbl_items,id',
                    'transaction_id' => 'required|string',
                    'customer_id' => 'required|exists:tbl_customers,id',
                    'quantity' => 'required|numeric|min:1',
                    'transaction_date' => 'required|date',
                    'encoded_by' => 'nullable|string',
                    'user_id' => 'required|exists:tbl_system_users,id'
                ]
            );

            $transactions = Transactions::create($validationInput);
            return response()->json([
                'success' => true,
                'customers' =>  $transactions
            ]);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
            //throw $th;
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id) {
        try {
            $transactions = Transactions::find($id);
            if (!$transactions) {
                return response()->json(['success' => false, 'message' => 'transaction not found'], 404);
            }

            $validationInput = $request->validate(
                [
                    'item_id' => 'required|exists:tbl_items,id',
                    'transaction_id' => 'required|string',
                    'customer_id' => 'required|exists:tbl_customers,id',
                    'quantity' => 'required|numeric|min:1',
                    'transaction_date' => 'required|date',
                    'user_id' => 'required|exists:tbl_system_users,id'
                ]
            );

            $transactions->update($validationInput);
            return response()->json([
                'success' => true,
                'customers' =>  $transactions
            ]);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
            //throw $th;
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }

    }

    public function destroy($id) {
        try {
            $transactions = Transactions::find($id);
            if (!$transactions) {
                return response()->json(['success' => false, 'message' => 'transaction not found'], 404);
            }
            $transactions->delete();
            return response()->json([
                'success' => true,
                'customers' =>  $transactions
            ],200);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
            //throw $th;
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }

}
