<?php

namespace App\Http\Controllers;

use App\Events\BadgeUpdated;
use App\Models\Assistances;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\vw_patient_billing;
use App\Services\BadgeService;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class BillingController extends Controller
{
    protected $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    public function index()
    {

        $billing  = vw_patient_billing::select(
            'patient_id',
            'firstname',
            'lastname',
            'middlename',
            'ext',
            'birthdate',
            'contact_number',
            'age',
            'barangay',
            'transaction_id',
            'transaction_type',
            'transaction_status',
        )->get();

        return response()->json($billing);
    }


    public function billing_report()
    {
        // Get all billing data
        $billing = DB::table('vw_billing_report')->select(
            'patient_id',
            'firstname',
            'lastname',
            'middlename',
            'ext',
            'birthdate',
            'contact_number',
            'age',
            'barangay'
        )->get();

        // Use unique() to filter by patient_id
        $uniquePatients = $billing->unique('patient_id')->values();

        return response()->json($uniquePatients);
    }

    // fetching the billing of the patient base on his transaction id
    // billing of patient
    public function billingPatient($transactionId, Request $request)
    {
        $result = $this->billingService->billing($transactionId, $request);

        return $result;
    }


    // this function for the update the status of the transaction to complete to proceed on the guarantee
    // updating the transaction on the billing into complete
    public function TransactionUpdate(Request $request, $transactionId)
    {

        //  Validate request
        $validated = $request->validate([
            'status' => 'required|in:Complete'
        ]);

        $result = $this->billingService->update($validated, $transactionId, $request);

        // ✅ Then broadcast the fresh counts AFTER the DB has changed
        $counts = app(BadgeService::class)->getBadgeCounts();
        broadcast(new BadgeUpdated($counts));

        return $result;
    }
}
