<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Models\Daily_inventory as Inventory;
use Illuminate\Support\Facades\DB;

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

            $Inventory = Inventory::where('id', $id)
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

    public function showLatest()
    {
        try {
            $latestInventoryQuery = DB::table('tbl_daily_inventory as inv1')
                ->select('inv1.id', 'inv1.stock_id', 'inv1.Openning_quantity', 'inv1.Closing_quantity', 'inv1.quantity_out', 'inv1.transaction_date', 'inv1.remarks', 'inv1.status', 'inv1.user_id')
                ->whereRaw('inv1.transaction_date = (
                SELECT MAX(inv2.transaction_date)
                FROM tbl_daily_inventory as inv2
                WHERE inv2.stock_id = inv1.stock_id
            )');

            $data = $latestInventoryQuery->get();
            return response()->json($data);
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
            $Inventory = Inventory::where('transaction_date', $transaction_date)
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
                    'stock_id' => 'required|exists:tbl_items,id',
                    'Openning_quantity' => 'required|numeric|min:1',
                    'Closing_quantity' => 'nullable|numeric',
                    'quantity_out' => 'nullable|numeric',
                    'transaction_date' => 'required|date',
                    'remarks'   => 'nullable|string|max:250',
                    'status'   => 'nullable|string|max:250',
                    'user_id' => 'required|exists:tbl_system_users,id',
                ]
            );

            $System_users = Inventory::create($validationInput);
            return response()->json([
                'success' => true,
                'dailyInventory' =>  $System_users
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

    public function update(Request $request, $id)
    {
        try {
            $Inventory = Inventory::where('id', $id);
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
                    'remarks'   => 'nullable|string|max:250',
                    'status'   => 'nullable|string|max:250',
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

    public function destroy($id)
    {
        try {
            Inventory::where('id', $id)->delete();
            // if (!$Inventory) {
            //     return response()->json(['success' => false, 'message' => 'transaction not found'], 404);
            // }

            return response()->json([
                'success' => true,
                'message' => 'record deleted successful'
            ], 200);
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

    public function getLowQuantityStocks()
    {
        try {
            $lowStocks =  Inventory::where('Openning_quantity', '<', 20)
                ->orWhere('Closing_quantity', '<', 20)
                ->get();

            return response()->json([
                'success' => true,
                'stocks' => $lowStocks
            ], 200);
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

    public function regenerateInventory()
    {
        try {
            // Fetch latest inventory data
            $latestInventoryQuery = DB::table('tbl_daily_inventory as inv1')
                ->select('inv1.id', 'inv1.stock_id', 'inv1.Closing_quantity', 'inv1.transaction_date')
                ->whereRaw('inv1.transaction_date = (
                SELECT MAX(inv2.transaction_date)
                FROM tbl_daily_inventory as inv2
                WHERE inv2.stock_id = inv1.stock_id
            )');

            $latestData = $latestInventoryQuery->get();

            // Insert new records with updated quantities
            $insertData = $latestData->map(function ($item) {
                return [
                    'stock_id' => $item->stock_id,
                    'Openning_quantity' => $item->Closing_quantity,
                    'Closing_quantity' => $item->Closing_quantity, // Will be calculated later
                    'quantity_out' => 0,
                    'transaction_date' => now(), // Use current date
                    'remarks' => 'Auto-generated from previous closing',
                    'status' => 'OPEN',
                    'user_id' => 1, // Default user ID
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray();

            DB::table('tbl_daily_inventory')->insert($insertData);

            return response()->json([
                'success' => true,
                'message' => 'New inventory records generated successfully',
                'count' => count($insertData),
            ], 201);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function closeInventory()
    {
        try {

            // Fetch the latest inventory data for each stock_id
            $latestInventoryQuery = DB::table('tbl_daily_inventory as inv1')
                ->select('inv1.id', 'inv1.stock_id', 'inv1.Closing_quantity', 'inv1.transaction_date')
                ->whereRaw('inv1.transaction_date = (
            SELECT MAX(inv2.transaction_date)
            FROM tbl_daily_inventory as inv2
            WHERE inv2.stock_id = inv1.stock_id
        )');

            $latestData = $latestInventoryQuery->get();

            // Validate if there are records to update
            if ($latestData->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No latest inventory records found to close.',
                ], 404);
            }

            // Prepare data for update
            $updateData = $latestData->map(function ($item) {
                return [
                    'id' => $item->id,
                    'status' => 'CLOSE',
                    'updated_at' => now(), // Update the timestamp
                ];
            })->toArray();

            // Perform the bulk update using a transaction for atomicity
            DB::transaction(function () use ($updateData) {
                foreach ($updateData as $data) {
                    DB::table('tbl_daily_inventory')
                        ->where('id', $data['id'])
                        ->update([
                            'status' => $data['status'],
                            'updated_at' => $data['updated_at'],
                        ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Latest inventory records closed successfully.',
                'count' => count($updateData),
            ], 200);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }


}
