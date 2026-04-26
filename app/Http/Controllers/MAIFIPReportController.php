<?php

namespace App\Http\Controllers;

use App\Http\Resources\MedicalAssistanceResource;
use App\Models\Transaction;
use App\Services\ReportService;
use Illuminate\Http\Request;

class MAIFIPReportController extends Controller
{
    //

      protected $reportService;

      public function __construct(ReportService $reportService)
      {
            $this->reportService = $reportService;
      }


    public function report(Request $request)
    {
        $request->validate([
            'fromDate'   => 'nullable|date',
            'toDate'     => 'nullable|date',
            'singleDate' => 'nullable|date',
        ]);

        $query = Transaction::where('status', 'Funded')
            ->with(['assistance.funds', 'patient:id,firstname,lastname,middlename']);

        // ✅ if singleDate is provided, use it
        if ($request->filled('singleDate')) {
            $query->whereDate('transaction_date', $request->singleDate);
        }
        // ✅ else use date range if fromDate & toDate are given
        elseif ($request->filled('fromDate') && $request->filled('toDate')) {
            $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
        }

        $transactions = $query->get();

        $result = $transactions->map(function ($t) {
            return [
                'transaction_id'   => $t->id,
                'Gl_number'        => $t->assistance->gl_number ?? null,
                'transaction_date' => $t->transaction_date,
                'patient_name'     => trim($t->patient->firstname . ' ' . $t->patient->middlename . ' ' . $t->patient->lastname),
                // only MAIFIP funds
                'maifip_funds'     => $t->assistance
                    ? $t->assistance->funds->where('fund_source', 'MAIFIP')->values()
                    : [],
            ];
        });

        return response()->json($result);
    }

    public function report_index()
    {
        $transactions = Transaction::where('status', 'Funded')
            ->whereHas('assistance', function ($query) {
                $query->whereNotNull('gl_lgu')
                    ->orWhereNotNull('gl_cong');
            })
            ->with(['assistance.funds', 'patient:id,firstname,lastname,middlename'])
            ->get();

        $result = $transactions->map(function ($t) {
            return [
                'transaction_id'   => $t->id,
                'gl_lgu'           => $t->assistance->gl_lgu ?? null,
                'gl_cong'          => $t->assistance->gl_cong ?? null,
                'transaction_date' => $t->transaction_date,
                'patient_name'     => trim($t->patient->firstname . ' ' . $t->patient->middlename . ' ' . $t->patient->lastname),
                // only MAIFIP funds
                'maifip_LGU'       => $t->assistance
                    ? $t->assistance->funds->where('fund_source', 'MAIFIP-LGU')->values()
                    : [],
                'maifip_Congressman' => $t->assistance
                    ? $t->assistance->funds->where('fund_source', 'MAIFIP-Congressman')->values()
                    : [],
            ];
        })
        ->sortBy([
            ['gl_cong', 'asc'], // ✅ sort gl_cong lowest to highest first
            ['gl_lgu',  'asc'], // ✅ then gl_lgu lowest to highest
        ])
            ->values(); // ✅ reindex array keys after sorting



        return response()->json($result);
    }

    // DOH report maifp
    public function medicalAssistanceReport(){

     $result =  $this->reportService->medicalAssistanceReportMaip();

        return MedicalAssistanceResource::collection($result);

    }



    // DOH report maifp
    public function dohReport(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array'
        ]);

        $result =  $this->reportService->exportExcelReport($request,$validated);

        return $result;
    }

}
