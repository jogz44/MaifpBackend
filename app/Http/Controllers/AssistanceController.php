<?php

namespace App\Http\Controllers;

use App\Models\Assistances;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\AssistanceRequest;
use App\Models\vw_fund_sources_summary;
use App\Models\vw_PatientAssistanceFunds;

class AssistanceController extends Controller
{
    //

    public function store(AssistanceRequest $request)
    {
        $validated = $request->validated();

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
            'status' => $validated['status'] ?? null,
        ]);

        //     // Attach funds
            // foreach ($validated['assistances'] as $fund) {
            //     $assistance->funds()->create([
            //         'fund_source' => $fund['fund_source'],
            //         'fund_amount' => $fund['fund_amount'] ?? null,
            //     ]);
            // }

        //     return response()->json([
        //         'message' => 'Successfully created assistance with funds',
        //         'assistance' => $assistance->load('funds')
        //     ]);
        // }

        // // Attach funds
        foreach ($validated['assistances'] as $fund) {
            $assistance->funds()->create([
                'fund_source' => $fund['fund_source'],
                'fund_amount' => $fund['fund_amount'] ?? null,
            ]);
        }

        // 🔹 If assistance is completed, mark transaction as complete
        if ($assistance->status === 'Complete') {
            Transaction::where('id', $assistance->transaction_id)
                ->update(['status' => 'Complete']);
        }

        return response()->json([
            'message'    => 'Successfully created assistance with funds',
            'assistance' => $assistance->load('funds')
        ]);
    }


    public function  index()
    {

        $patient = vw_PatientAssistanceFunds::all();
        return response()->json($patient);
    }

    public function  funds()
    {
        $funds = vw_fund_sources_summary::all();
        return response()->json($funds);
    }
}
