<?php

namespace App\Http\Controllers;

use App\Models\RequisitionIssuanceSlip;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RequisitionIssuanceSlipController extends Controller
{
    //
    public function index() {}

    public function show($id) {}

    public function store(Request $request)
    {
        try {
            // Properly merge Auth ID into request data
            $request->merge(['userid' => Auth::id()]);

            $validated = $request->validate(
                [
                    'transaction_date' => 'required|date',
                    'purpose' => 'required|string|max:500',
                    'ris_id' => 'required|string',
                    'userid' => 'required|exists:users,id'
                ]
            );

            $ris = RequisitionIssuanceSlip::create($validated);
            return response()->json(['success' => true, 'message' => 'Successfuly Saved.', 'RIS' => $ris], 200);
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
        } catch (Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update($id, Request $request) {}

    public function delete($id) {}
}
