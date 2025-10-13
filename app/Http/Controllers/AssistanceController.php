<?php

namespace App\Http\Controllers;

use App\Models\Assistances;
use App\Models\Transaction;
use App\Models\vw_fund_sources_summary;
use App\Http\Requests\AssistanceRequest;


class AssistanceController extends Controller
{
    //
    // public function store(AssistanceRequest $request)
    // {
    //     $validated = $request->validated();

    //     // Encode arrays as JSON
    //     $medication = isset($validated['medication']) ? json_encode($validated['medication']) : null;
    //     // $laboratories = isset($validated['laboratories_details']) ? json_encode($validated['laboratories_details']) : null;
    //     $radiology = isset($validated['radiology_details']) ? json_encode($validated['radiology_details']) : null;
    //     $ultrasound = isset($validated['ultrasound_details']) ? json_encode($validated['ultrasound_details']) : null;
    //     $mammogram = isset($validated['mammogram_details']) ? json_encode($validated['mammogram_details']) : null;
    //     $examination = isset($validated['examination_details']) ? json_encode($validated['examination_details']) : null;

    //     // Create the main assistance record
    //     $assistance = Assistances::create([
    //         'patient_id' => $validated['patient_id'],
    //         'transaction_id' => $validated['transaction_id'],
    //         'consultation_amount' => $validated['consultation_amount'] ?? null,
    //         // 'laboratory_total' => $validated['laboratory_total'] ?? null,
    //         'radiology_total' => $validated['radiology_total'] ?? null,
    //         'examination_total' => $validated['examination_total'] ?? null,
    //         'ultrasound_total' => $validated['ultrasound_total'] ?? null,
    //         'mammogram_total' => $validated['mammogram_total'] ?? null,
    //         'medication_total' => $validated['medication_total'] ?? null,

    //         'total_billing' => $validated['total_billing'] ?? null,
    //         'discount' => $validated['discount'] ?? null,
    //         'final_billing' => $validated['final_billing'] ?? null,
    //        'medication' => $medication,
    //         // 'laboratories_details' => $laboratories,
    //         'ultrasound_details' => $ultrasound,
    //         'mammogram_details' => $mammogram,
    //         'radiology_details' => $radiology,
    //         'examination_details' => $examination,
    //         // 'status' => $validated['status'] ?? null,
    //     ]);

    //     // Attach funds
    //     foreach ($validated['assistances'] as $fund) {
    //         $assistance->funds()->create([
    //             'fund_source' => $fund['fund_source'],
    //             'fund_amount' => $fund['fund_amount'] ?? null,
    //         ]);
    //     }

    //     return response()->json([
    //         'message'    => 'Successfully created assistance with funds, labs, and medications',
    //         'assistance' => $assistance->load('funds')
    //     ]);
    // }
    public function store(AssistanceRequest $request)
    {
        $validated = $request->validated();

        // Encode arrays as JSON or set null if missing/empty
        $medication = !empty($validated['medication']) ? json_encode($validated['medication']) : null;
        $radiology = !empty($validated['radiology_details']) ? json_encode($validated['radiology_details']) : null;
        $ultrasound = !empty($validated['ultrasound_details']) ? json_encode($validated['ultrasound_details']) : null;
        $mammogram = !empty($validated['mammogram_details']) ? json_encode($validated['mammogram_details']) : null;
        $examination = !empty($validated['examination_details']) ? json_encode($validated['examination_details']) : null;

        // Create the main assistance record
        $assistance = Assistances::create([
            'patient_id'          => $validated['patient_id'] ?? null,
            'transaction_id'      => $validated['transaction_id'] ?? null,
            'consultation_amount' => $validated['consultation_amount'] ?? null,
            'radiology_total'     => $validated['radiology_total'] ?? null,
            'examination_total'   => $validated['examination_total'] ?? null,
            'ultrasound_total'    => $validated['ultrasound_total'] ?? null,
            'mammogram_total'     => $validated['mammogram_total'] ?? null,
            'medication_total'    => $validated['medication_total'] ?? null,
            'total_billing'       => $validated['total_billing'] ?? null,
            'discount'            => $validated['discount'] ?? null,
            'final_billing'       => $validated['final_billing'] ?? null,
            'medication'          => $medication,
            'ultrasound_details'  => $ultrasound,
            'mammogram_details'   => $mammogram,
            'radiology_details'   => $radiology,
            'examination_details' => $examination,
        ]);

        // ✅ Attach funds only if there are any
        if (!empty($validated['assistances']) && is_array($validated['assistances'])) {
            foreach ($validated['assistances'] as $fund) {
                $assistance->funds()->create([
                    'fund_source' => $fund['fund_source'] ?? null,
                    'fund_amount' => $fund['fund_amount'] ?? null,
                ]);
            }
        }

        return response()->json([
            'message'    => 'Successfully created assistance with funds, labs, and medications',
            'assistance' => $assistance->load('funds'),
        ]);
    }

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
