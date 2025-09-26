<?php

namespace App\Http\Controllers;

use App\Models\Assistances;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\vw_fund_sources_summary;
use App\Http\Requests\AssistanceRequest;
use App\Models\vw_PatientAssistanceFunds;

class AssistanceController extends Controller
{
    //
    public function store(AssistanceRequest $request)
    {
        $validated = $request->validated();

        // Encode arrays as JSON
        $medication = isset($validated['medication']) ? json_encode($validated['medication']) : null;
        $laboratories = isset($validated['laboratories_details']) ? json_encode($validated['laboratories_details']) : null;

        // Create the main assistance record
        $assistance = Assistances::create([
            'patient_id' => $validated['patient_id'],
            'transaction_id' => $validated['transaction_id'],
            'consultation_amount' => $validated['consultation_amount'] ?? null,
            'laboratory_total' => $validated['laboratory_total'] ?? null,
            'medication_total' => $validated['medication_total'] ?? null,
            'total_billing' => $validated['total_billing'] ?? null,
            'discount' => $validated['discount'] ?? null,
            'final_billing' => $validated['final_billing'] ?? null,
            'medication' => $medication,
            'laboratories_details' => $laboratories,
            // 'status' => $validated['status'] ?? null,
        ]);

        // Attach funds
        foreach ($validated['assistances'] as $fund) {
            $assistance->funds()->create([
                'fund_source' => $fund['fund_source'],
                'fund_amount' => $fund['fund_amount'] ?? null,
            ]);
        }

        return response()->json([
            'message'    => 'Successfully created assistance with funds, labs, and medications',
            'assistance' => $assistance->load('funds')
        ]);
    }



    // public function  index()
    // {

    //     $patient = vw_PatientAssistanceFunds::all();
    //     return response()->json($patient);
    // }


    public function index()
    {
        $transactions = Transaction::where('status', 'Funded')
            ->with(['assistance.funds', 'patient:id,firstname,lastname,middlename'])
            ->get();

        $result = $transactions->map(function ($t) {
            return [
                'transaction_id' => $t->id,
                'transaction_date' => $t->transaction_date,
                'patient_name'   => trim($t->patient->firstname . ' ' . $t->patient->middlename . ' ' . $t->patient->lastname),
                'funds'          => $t->assistance ? $t->assistance->funds : [],
            ];
        });

        return response()->json($result);
    }



    public function  funds()
    {
        $funds = vw_fund_sources_summary::all();
        return response()->json($funds);
    }


}
