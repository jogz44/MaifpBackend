<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Models\IndicatorLibrary as Indicator;
use Illuminate\Validation\ValidationException;

class IndicatorLibraryController extends Controller
{
    //

    public function getCurrentStatus()
    {
        try {
            $indicator = Indicator::latest('transaction_date')->first();

            if (!$indicator) {
                return response()->json([
                    'message' => 'No indicators found for this date',
                ], 404);
            }


            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_date' => $indicator->transaction_date,
                    'is_open' => $indicator->is_open,
                    'is_close' => $indicator->is_close,
                ],
            ], 200);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'errors' => $qe->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }
    }

   

    public function index()
    {

        try {
            $indicator = Indicator::all();
            response()->json($indicator, 200);
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
                'errors' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {

        try {
            $indicator = Indicator::where('id', $id);
            response()->json($indicator, 200);
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
                'errors' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    public function store()
    {
        try {
            $existingIndicator = Indicator::where('transaction_date', now()->format('Y-m-d'))->first();

            if ($existingIndicator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Indicator for today already exists',
                ], 400);
            }

            Indicator::create([
                'transaction_date' => now()->format('Y-m-d'),
                'is_open' => true,
                'is_close' => false
            ]);
            return response()->json(['success' => 'Stock OPEN'], 200);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'errors' => $qe->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }
    }


    public function update()
    {
        try {
            $indicator = Indicator::where('transaction_date', now()->format('Y-m-d'))->first();
    
            if (!$indicator) {
                return response()->json([
                    'success' => false,
                    'message' => 'No indicators found for today',
                ], 404);
            }
    
            // You might want to keep the original date if updating based on it
            // $indicator->update([
            //     'is_open' => false,
            //     'is_close' => true,
            // ]);

            $indicator->is_open = false;
            $indicator->is_close = true;
            $indicator->save();
    
            return response()->json(['success' => 'Stock Close'], 200);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'errors' => $qe->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }
    }
    

    public function destroy($id)
    {
        try {
            $indicator = Indicator::where('id', $id);
            $indicator->delete();
            response()->json(['message' => 'Stock Close/Open indicator deleted.'], 200);
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
                'errors' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }
    }
}
