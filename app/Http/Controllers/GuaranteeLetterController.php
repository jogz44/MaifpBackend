<?php

namespace App\Http\Controllers;


use App\Events\BadgeUpdated;
use App\Http\Requests\GuaranteeLetterRequest;
use App\Http\Requests\GuaranteeLetterUpdateRequest;
use App\Models\Assistances;
use App\Models\Budget;
use App\Models\GuaranteeLetter;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\vw_guarantee_patients;
use App\Services\BadgeService;
use App\Services\GuaranteeLetterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuaranteeLetterController extends Controller
{

    protected $guaranteeLetterService;
    public function __construct(GuaranteeLetterService $guaranteeLetterService)
    {
        $this->guaranteeLetterService = $guaranteeLetterService;
    }

    public function guaranteeLetter($transactionId)
    {
        $transaction = Assistances::with([
            'Funds',
            'transaction',
            'patient' => function ($query) {
                $query->select(
                    'id',
                    'lastname',
                    'firstname',
                    'gender',
                    'age',
                    'middlename',
                    'birthdate',
                    'purok',
                    'street',
                    'barangay',
                    'city',
                    'province'
                );
            }
        ])
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        // Decode JSON fields
        $transaction->medication = json_decode($transaction->medication, true);
        $transaction->radiology_details = json_decode($transaction->radiology_details, true);
        $transaction->ultrasound_details = json_decode($transaction->ultrasound_details, true);
        $transaction->mammogram_details = json_decode($transaction->mammogram_details, true);
        $transaction->examination_details = json_decode($transaction->examination_details, true);


        // ✅ Combine purok, street, and barangay into address
        if ($transaction->patient) {
            $transaction->patient->address = trim(
                collect([
                    $transaction->patient->purok,
                    $transaction->patient->street,
                    $transaction->patient->barangay,
                    $transaction->patient->city,
                    $transaction->patient->province,
                ])->filter()->implode(', ')
            );

            // Optionally hide the original fields
            unset($transaction->patient->purok);
            unset($transaction->patient->street);
            unset($transaction->patient->barangay);
            unset($transaction->patient->city);
            unset($transaction->patient->province);
        }

        return response()->json($transaction);
    }


    public function index()
    {
        $transactions = Transaction::where('status', 'Complete')
            ->with(['patient:id,firstname,lastname,middlename,ext,birthdate,age,contact_number,barangay'])
            ->with(['assistances.funds'])
            ->select([
                'id', // don't alias
                'patient_id',
                'transaction_type',
                'transaction_date',
                'status'
            ])
            ->get()
            ->map(function ($transaction) {
                // flatten all fund_sources for this transaction
                $fundSources = $transaction->assistances
                    ->flatMap(function ($assistance) {
                        return $assistance->funds->pluck('fund_source');
                    })
                    ->toArray();

                $transaction->isMAIFIP_LGU = in_array('MAIFIP-LGU', $fundSources) ? 1 : 0;
                $transaction->isMAIFIP_Congressman = in_array('MAIFIP-Congressman', $fundSources) ? 1 : 0;

                // optionally rename id to transaction_id
                $transaction->transaction_id = $transaction->id;

                unset($transaction->assistances);

                return $transaction;
            });


        return response()->json($transactions);
    }

    // get the max gl number
    public function getMaxGLNumber()
    {
        $result = $this->guaranteeLetterService->getGLNumber();

        return $result;

    }

    // updating the assistance
    public function updateAssistance(GuaranteeLetterUpdateRequest $request, $transaction_id)
    {
        // ✅ Validate inputs
        $validated = $request->validated();

        $result = $this->guaranteeLetterService->update($validated,$transaction_id);

        // ✅ Then broadcast the fresh counts AFTER the DB has changed
        $counts = app(BadgeService::class)->getBadgeCounts();
        broadcast(new BadgeUpdated($counts));

        return $result;
    }

    // updating the transaction status to Funded
    public function updateFunded(Request $request,$transactionId)
    {
        // ✅ Validation
        $validated = $request->validate([
            'status' => 'required|in:Funded'
        ]);

        $result = $this->guaranteeLetterService->updateStatus($transactionId,$validated);

        // ✅ Then broadcast the fresh counts AFTER the DB has changed
        $counts = app(BadgeService::class)->getBadgeCounts();
        broadcast(new BadgeUpdated($counts));

        return $result;
    }
}
