<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class MAIFIPReportController extends Controller
{
    //
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
        });

        return response()->json($result);
    }


    // public function report_index()
    // {

    //     $query = Transaction::where('status', 'Funded')
    //         ->with(['assistance.funds', 'patient:id,firstname,lastname,middlename']);


    //     $transactions = $query->get();

    //     $result = $transactions->map(function ($t) {
    //         return [
    //             'transaction_id'   => $t->id,
    //             'gl_lgu'        => $t->assistance->gl_lgu ?? null,
    //             'gl_cong'        => $t->assistance->gl_cong ?? null,

    //             'transaction_date' => $t->transaction_date,
    //             'patient_name'     => trim($t->patient->firstname . ' ' . $t->patient->middlename . ' ' . $t->patient->lastname),
    //             // only MAIFIP funds
    //             'maifip_LGU'     => $t->assistance
    //                 ? $t->assistance->funds->where('fund_source', 'MAIFIP-LGU')

    //                 ->values()
    //                 : [],
    //             'maifip_Congressman'     => $t->assistance
    //                 ? $t->assistance->funds->where('fund_source', 'MAIFIP-Congressman')

    //                 ->values()
    //                 : [],
    //         ];
    //     });

    //     return response()->json($result);
    // }
}
