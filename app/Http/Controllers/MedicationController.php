<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Medication;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Medication_details;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\MedicationRequest;


class MedicationController extends Controller
{

    public function index_view()
    {
        $data = DB::table('vw_patient_medication')->get();
        return response()->json($data);
    }
    // this method is for Medication will fetch the patient need to Medication
    public function qualifiedTransactionsMedication()
    {
        try {
            $transactions = Transaction::where('status', 'qualified')
                ->where(function ($query) {
                    $query->where('transaction_type', 'Medication')
                        ->orWhereHas('consultation', function ($q) {
                            $q->where('status', 'Medication');
                        });
                })
                // ->whereDate('transaction_date', now()->toDateString()) // ✅ today's transactions
                ->whereDoesntHave('medication', function ($q) {
                    $q->where('status', 'Done'); // ❌ exclude if medication is Done
                })
                ->with([
                    'patient',
                    'consultation'
                ])
                ->get()
                ->groupBy('patient_id')
                ->map(function ($group) {
                    $patient = $group->first()->patient;

                    // attach transactions to patient
                    $patient->transaction = $group->map(function ($transaction) {
                        return collect($transaction)->except('patient');
                    })->values();

                    return $patient;
                })
                ->values();

            return response()->json($transactions);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch qualified transactions.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    // public function qualifiedTransactionsMedication()
    // {
    //     try {
    //         // Base query with joins
    //         $transactions = DB::table('transaction as t')
    //             ->leftJoin('patient as p', 'p.id', '=', 't.patient_id')
    //             ->leftJoin('new_consultation as c', 'c.transaction_id', '=', 't.id')
    //             ->leftJoin('medication as m', 'm.transaction_id', '=', 't.id')
    //             ->select(
    //                 't.id as transaction_id',
    //                 't.patient_id',
    //                 't.transaction_type',
    //                 't.status as transaction_status',
    //                 't.transaction_date',
    //                 'c.id as consultation_id',
    //                 'c.status as consultation_status',
    //                 'm.id as medication_id',
    //                 'm.status as medication_status',
    //                 'p.id as patient_id',
    //                 'p.firstname',
    //                 'p.lastname',
    //                 'p.middlename',
    //                 'p.ext',
    //                 'p.birthdate',
    //                 'p.contact_number',
    //                 'p.age',
    //                 'p.gender'
    //             )
    //             ->where('t.status', 'qualified')
    //             ->where(function ($query) {
    //                 $query->where('t.transaction_type', 'Medication')
    //                     ->orWhere('c.status', 'Medication');
    //             })
    //             ->where(function ($query) {
    //                 $query->whereNull('m.id')
    //                     ->orWhere('m.status', '<>', 'Done'); // exclude "Done"
    //             })
    //             ->orderBy('t.patient_id')
    //             ->get();

    //         // Group by patient
    //         $grouped = $transactions->groupBy('patient_id')->map(function ($group) {
    //             $patient = [
    //                 'id'            => $group->first()->patient_id,
    //                 'firstname'     => $group->first()->firstname,
    //                 'lastname'      => $group->first()->lastname,
    //                 'middlename'    => $group->first()->middlename,
    //                 'ext'           => $group->first()->ext,
    //                 'birthdate'     => $group->first()->birthdate,
    //                 'contact_number' => $group->first()->contact_number,
    //                 'age'           => $group->first()->age,
    //                 'gender'        => $group->first()->gender,
    //             ];

    //             $patient['transaction'] = $group->map(function ($row) {
    //                 return [
    //                     'transaction_id'    => $row->transaction_id,
    //                     'transaction_type'  => $row->transaction_type,
    //                     'transaction_status' => $row->transaction_status,
    //                     'transaction_date'  => $row->transaction_date,
    //                     'consultation_id'   => $row->consultation_id,
    //                     'consultation_status' => $row->consultation_status,
    //                     'medication_id'     => $row->medication_id,
    //                     'medication_status' => $row->medication_status,
    //                 ];
    //             })->values();

    //             return $patient;
    //         })->values();

    //         return response()->json($grouped);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch qualified transactions.',
    //             'error' => $th->getMessage()
    //         ], 500);
    //     }
    // }

    public function store(MedicationRequest $request) // store the medations of the patient with his transaction id
    {
        $validated = $request->validated();

        // Fetch transaction with consultation
        // $transaction = Transaction::with('consultation')
        //     ->findOrFail($validated['transaction_id']);

        //If consultation exists, add it to validated data
        // if ($transaction->consultation) {
        //     $validated['new_consultation_id'] = $transaction->consultation->id;
        // }

        //  current date
        $validated['transaction_date'] = Carbon::now();

        //Create medication with complete data
        $medication = Medication_details::create($validated);

        return response()->json($medication);
    }



    // this method for the status on the Medication of the transaction_id this method i use for testing
    public function status(Request $request)
    {
        // ✅ validate request
        $validated = $request->validate([
            'status' => 'required|in:Done,Pending',
            'transaction_id' => 'required|exists:transaction,id',
            // 'medication_detials_id' => 'required|exists:medication_details,id'
        ]);

        // ✅ Update or create transaction status
        $transactionStatus = Medication::updateOrCreate(
            ['transaction_id' => $validated['transaction_id']], // condition
            ['status' => $validated['status']]                 // values to update
        );


        return response()->json([
            'success' => true,
            'message' => 'All Medicine under this transaction updated successfully.',
            'data' => $transactionStatus
        ]);
    }

}
