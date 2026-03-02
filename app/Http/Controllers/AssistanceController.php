<?php

namespace App\Http\Controllers;

use App\Events\BadgeUpdated;
use App\Http\Requests\AssistanceRequest;
use App\Models\Assistances;
use App\Models\Transaction;
use App\Models\vw_fund_sources_summary;
use App\Services\AssistanceService;
use App\Services\BadgeService;
use Illuminate\Support\Facades\Auth;


class AssistanceController extends Controller
{

    protected $assistanceService;

    public function __construct(AssistanceService $assistanceService)
    {
        $this->assistanceService = $assistanceService;
    }

    // storing assistance
    public function storeAssistance(AssistanceRequest $request)
    {
        $validated = $request->validated();

        $result = $this->assistanceService->store($validated, $request);

        // ✅ Then broadcast the fresh counts AFTER the DB has changed
        $counts = app(BadgeService::class)->getBadgeCounts();
        broadcast(new BadgeUpdated($counts));

        return $result;
    }

    public function  funds()
    {
        $funds = vw_fund_sources_summary::all();
        return response()->json($funds);
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
}
