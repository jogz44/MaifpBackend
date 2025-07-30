<?php

namespace App\Http\Controllers;

use App\Models\RequisitionIssuanceSlip;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class RequisitionIssuanceSlipController extends Controller
{
    //
    public function RIS_id()
    {
        $currendate = Carbon::today()->format("Ymd");
        $latestId = RequisitionIssuanceSlip::max('id') ?? 0;
        $nextId = $latestId + 1;
        $transactionNumber = str_pad($nextId, 6, '0', STR_PAD_LEFT);

        return "RIS-{$currendate}-{$transactionNumber}";
    }
    public function index()
    {
    }

    public function show($id)
    {
    }

    public function store(Request $request)
    {
        try {
            $currendate = Carbon::today()->toDateString();

            $latestId = RequisitionIssuanceSlip::max('id') ?? 0;
            $nextId = $latestId + 1;
            $transactionNumber = str_pad($nextId, 6, '0', STR_PAD_LEFT);
            $ris_id = "RIS-{$currendate}-{$transactionNumber}";


            // Properly merge Auth ID into request data
            $request->merge(['userid' => Auth::id()]);
            $request->merge(['transaction_date' => $currendate]);
            $request->merge(['ris_id'=> $ris_id]);


            $validated = $request->validate(
                [
                    'transaction_date' => 'required|date',
                    'purpose' => 'required|string|max:500',
                    'ris_id' => 'required|string',
                    'userid' => 'required|exists:users,id'
                ]
            );

            $ris = RequisitionIssuanceSlip::create($validated);

            return response()->json(['success' => true, 'message' => 'Successfuly Saved.', 'ris_id' => $ris->ris_id], 200);
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

    public function update($id, Request $request)
    {
    }

    public function delete($id)
    {
    }
}
