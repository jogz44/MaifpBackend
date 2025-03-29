<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Models\Items;


class ItemsController extends Controller
{
    //
    public function index()
    {
        try {

            $Items = Items::orderBy('id', 'desc')
                ->get();
            return response()->json(
                ['success' => true,
                 'items' =>  $Items],200);
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

            $item = Items::where('id',$id)->get();
            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Item not found'], 404);
            }
            return response()->json(['success' => true, 'items' =>  $item]);
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

    public function showItemsByPO($po_number)
    {

        try {

            $items = Items::where('po_no',$po_number)->get();
            if (!$items) {
                return response()->json(['success' => false, 'message' => 'Items not found'], 404);
            }
            return response()->json(['success' => true, 'items' =>  $items]);
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
                'po_no' => 'required|string|max:50',
                'brand_name' => 'required|string|max:100',
                'generic_name' => 'required|string|max:100',
                'dosage_form' => 'nullable|string|max:50',
                'dosage' => 'required|string|max:50',
                'category' => 'nullable|string|max:50',
                'unit' => 'required|string|max:50',
                'quantity' => 'required|numeric|min:1',
                'price' => 'nullable|numeric',
                'expiration_date' => 'required|date|after:today',
                'user_id' => 'required|exists:tbl_system_users,id',
                ]
            );

            $Items = Items::create($validationInput);
            return response()->json([
                'success' => true,
                'item' =>  $Items,
                'message'=> 'Item registration Successful'
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
            $item = Items::where($id)->get();
            if (!$item) {
                return response()->json(['success' => false, 'message' => 'item not found'], 404);
            }

            $validationInput = $request->validate(
                [
                    'po_no' => 'required|string|max:50',
                    'brand_name' => 'required|string|max:100',
                    'generic_name' => 'required|string|max:100',
                    'dosage_form' => 'nullable|string|max:50',
                    'dosage' => 'required|string|max:50',
                    'category' => 'nullable|string|max:50',
                    'unit' => 'required|string|max:50',
                    'price' =>'nullable|numeric',
                    'quantity' => 'required|numeric|min:1',
                    'price' => 'nullable|numeric',
                    'expiration_date' => 'required|date|after:today',
                    'user_id' => 'required|exists:tbl_system_users,id',
                ]
            );

            $item->update($validationInput);
            return response()->json([
                'success' => true,
                'item' =>  $item,
                'message'=> 'Item updating Successful'
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


    public function destroyItemsByPO($po_number) {
        try {
            $items = Items::where('po_no',$po_number)
                            ->get();
            if ($items->isEmpty()) //Used isEmpty() to check if the collection is empty.
            {
                return response()->json(['success' => false, 'message' => 'items not found'], 404);
            }
            // $item->delete();
            // Items::where('po_no',$po_number)->delete();
            $items->each->delete();  //Removed the redundant query by using $items->each->delete() to delete the items directly

            return response()->json([
                'success' => true,
                'message' => "Items under PO-number $po_number have been removed."
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

    public function destroy($id) {
        try {
            $item = Items::where('id',$id)->get();
            if (!$item) {
                return response()->json(['success' => false, 'message' => 'item not found'], 404);
            }
            $item->delete();
            return response()->json([
                'success' => true,
                'message' => 'item deleted successfully'
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

    public function getExpiringStock()
    {
        try{
            $monthFromNow = now()->addDays(30)->toDateString();

            $expiredItems = Items::where('expiration_date', '<',$monthFromNow)->get();
            return response()->json(['messege'=> 'success',
             'items' => $expiredItems,
             'month'=> $monthFromNow,
            'count' => $expiredItems->count(),
            // 'sql' => Items::where('expiration_date', '<', $monthFromNow)->toSql(),
            ],200);
        }catch (ValidationException $ve) {
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
