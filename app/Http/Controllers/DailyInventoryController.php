<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Models\Daily_inventory as Inventory;

class DailyInventoryController extends Controller
{
    //
    public function index()
    {
        try {

            $Inventory = Inventory::orderBy('transaction_date', 'desc')
                ->get();
            return response()->json(['success' => true, 'transactions' =>  $Inventory]);
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

            $Inventory = Inventory::find($id)
                ->get();
            return response()->json(['success' => true, 'transaction' =>  $Inventory]);
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

    public function showTodayInventory($transaction_date)
    {

        try {
            $Inventory = Inventory::where('transaction_date',$transaction_date)
                ->get();
            return response()->json(['success' => true, 'transaction' =>  $Inventory]);
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
                // 'po_no' => 'required|string|max:50',
                // 'brand_name' => 'required|string|max:100',
                // 'generic_name' => 'required|string|max:100',
                // 'dosage_form' => 'nullable|string|max:50',
                // 'dosage' => 'required|string|max:50',
                // 'category' => 'nullable|string|max:50',
                // 'unit' => 'required|string|max:50',
                // 'quantity' => 'required|numeric|min:1',
                // 'expiration_date' => 'required|date|after:today',
                // 'user_id' => 'required|exists:users,id',
                'stock_id' => 'required|exists:tbl_items,id',
                'Openning_quantity' => 'required|numeric|min:1',
                'Closing_quantity' => 'required|numeric|min:1',
                'quantity_out' => 'required|numeric|min:1',
                'transaction_date' => 'required|date',
                'user_id' => 'required|exists:tbl_system_users,id',
                ]
            );

            $System_users = Inventory::create($validationInput);
            return response()->json([
                'success' => true,
                'users' =>  $System_users
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
            $Inventory = Inventory::find($id);
            if (!$Inventory) {
                return response()->json(['success' => false, 'message' => 'transaction not found'], 404);
            }

            $validationInput = $request->validate(
                [
                    'stock_id' => 'required|exists:tbl_items,id',
                    'Openning_quantity' => 'required|numeric|min:1',
                    'Closing_quantity' => 'required|numeric|min:1',
                    'quantity_out' => 'required|numeric|min:1',
                    'transaction_date' => 'required|date',
                    'user_id' => 'required|exists:tbl_system_users,id',
                ]
            );

            $Inventory->update($validationInput);
            return response()->json([
                'success' => true,
                'transaction' =>  $Inventory
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
            $Inventory = Inventory::find($id);
            if (!$Inventory) {
                return response()->json(['success' => false, 'message' => 'transaction not found'], 404);
            }
            $Inventory->delete();
            return response()->json([
                'success' => true,
                'transaction' =>  $Inventory
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

    public function getLowQuantityStocks(){
        return  Inventory::where('Openning_quantity', '<', 20)
       ->orWhere('Closing_quantity', '<', 20 )
       ->get();
      }
}
