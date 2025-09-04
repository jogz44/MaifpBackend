<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\lib_laboratory;
use App\Models\New_Consultation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Validated;
use App\Http\Requests\LaboratoryRequest;
use App\Http\Requests\lib_laboratoryRequest;
use App\Models\Laboratories_details;

class LaboratoryController extends Controller
{
    // this method is for laboratory will fetch the patient need to laboratory
    public function qualifiedTransactionsLaboratory()
    {
        try {
            $transactions = Transaction::where('status', 'qualified')
                ->where(function ($query) {
                    $query->where('transaction_type', 'Laboratory')
                        ->orWhereHas('consultation', function ($q) {
                            $q->where('status', 'Processing');
                        });
                })
                //  Exclude transactions that already have laboratories with status = 'Done'
                ->whereDoesntHave('laboratories', function ($lab) {
                $lab->whereIn('status', ['Done', 'Returned', 'Pending']);
                })
                // ->whereDate('transaction_date', now()->toDateString()) // ✅ per transaction date (today)
                ->with([
                    'patient',
                    'vital',       // fetch vitals of the transaction
                    'consultation',
                    'laboratories' // fetch laboratories
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

    // // this method for the status on the laboratory that have connected on the consultation for the patient
    // public function status(Request $request, $transactionId)
    // {

    //     // validate request
    //     $validated = $request->validate([
    //         'status' => 'required|in:Done,Returned,Pending'
    //     ]);

    //     // find all labs by transaction_id
    //     $labs = Laboratory::where('transaction_id', $transactionId)->get();

    //     if ($labs->isEmpty()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No laboratories found for this transaction.'
    //         ], 404);
    //     }

    //     // update all labs
    //     foreach ($labs as $lab) {
    //         $lab->update($validated);

    //         // If lab is Returned, also update related consultation
    //         if ($lab->status === 'Returned' && $lab->new_consultation_id) {
    //             $consultation = New_Consultation::find($lab->new_consultation_id);

    //             if ($consultation) {
    //                 $consultation->status = 'Returned';
    //                 $consultation->save();
    //             }
    //         }
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'All laboratories under this transaction updated successfully.',
    //         'data' => $labs
    //     ]);
    // }


    // this method for updating the status on the laboratory that is connected to the consultation for the patient
    public function Laboratory_status(Request $request)
    {
        // validate request
        $validated = $request->validate([
            'status' => 'required|in:Done,Returned',
            'transaction_id' => 'required|exists:transaction,id',
        ]);

        // Update or create the main laboratory record
        $lab = Laboratory::updateOrCreate(
            ['transaction_id' => $validated['transaction_id']], // condition
            ['status' => $validated['status']]                  // update values
        );

        // ✅ Also update all laboratory_details linked to this transaction
        $labDetails = Laboratories_details::where('transaction_id', $validated['transaction_id'])->get();

        foreach ($labDetails as $detail) {
            // If Returned, update related consultation
            if ($validated['status'] === 'Returned' && $detail->new_consultation_id) {
                $consultation = New_Consultation::find($detail->new_consultation_id);
                if ($consultation) {
                    $consultation->status = 'Returned';
                    $consultation->save();
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Laboratory status under this transaction updated successfully.',
            'data' => $lab
        ]);
    }



    //  this method is for saving the laboratory of the patient with his transaction with amount
    public function store(LaboratoryRequest $request)
    {
        $user = Auth::user();

        $validated = $request->validated();

        // Check if transaction has consultation
        $transaction = \App\Models\Transaction::with('consultation')
            ->findOrFail($validated['transaction_id']);

        $newConsultationId = $transaction->consultation
            ? $transaction->consultation->id
            : null;

        $labs = [];

        foreach ($validated['laboratories'] as $labData) {
            $labs[] = Laboratories_details::create([
                'transaction_id' => $validated['transaction_id'],
                'new_consultation_id' => $newConsultationId, // set only if exists
                'laboratory_type' => $labData['laboratory_type'],
                'amount' => $labData['amount'],
                // 'status' => $labData['status'] ?? 'Pending',
            ]);
        }

        // Prepare log details
        $labDetails = collect($labs)->map(function ($lab) {
            return "{$lab->laboratory_type} (₱{$lab->amount})";
        })->implode(', ');

        // Log activity
        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($transaction) // better to log on the transaction
            ->withProperties([
                'ip'   => $request->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'labs' => $labs
            ])
            ->log("Added new laboratory services: {$labDetails}");


        return response()->json([
            'message' => 'Laboratories stored successfully',
            'laboratories' => $labs
        ]);
    }

    //for library laboratory store
    public function lib_laboratory_store(lib_laboratoryRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Check if laboratory already exists
        $existing = lib_laboratory::where('lab_name', $validated['lab_name'])->first();

        if ($existing) {
            return response()->json([
                'message' => 'already exists',
                'laboratory' => $existing,
            ], 200);
        }

        // Create new if not exists
        $laboratory = lib_laboratory::create($validated);

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($laboratory)
            ->withProperties([
                'ip' => $request->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
            ])
            ->log("Added New Services ".($laboratory ? "{$laboratory->lab_name} and  {$laboratory->lab_amount}" : "Unknown"));

        return response()->json([
            'message' => 'success',
            'laboratory' => $laboratory,
        ]);
    }

    // updating the library laboratory amount and type of services of laboratory
    public function lib_laboratory_update(lib_laboratoryRequest $request, $lib_laboratory)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $laboratory = lib_laboratory::findOrFail($lib_laboratory);

        // Capture old values before update
        $oldValues = $laboratory->getOriginal();

        // Update record
        $laboratory->update($validated);

        // 📝 Activity Log
        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($laboratory)
            ->withProperties([
                'ip' => $request->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'old' => $oldValues,
                'new' => $laboratory->getChanges(),
            ])
            ->log("Updated Services {$laboratory->lab_name}, Amount: {$laboratory->lab_amount}");

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated',
            'data' => $laboratory,
        ]);
    }

    //deleting the library laboratory
    public function lib_laboratory_delete($lib_laboratory, Request $request)
    {

        $user = Auth::user();

        $laboratory = lib_laboratory::findOrFail($lib_laboratory);

        $laboratory->delete($laboratory);

        // 📝 Activity Log for Delete
        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($laboratory)
            ->withProperties([
                'ip' => $request->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'deleted_record' => $laboratory,
            ])
            ->log(
                "Laboratory {$laboratory['lab_name']}, Amount: {$laboratory['lab_amount']} was deleted"

            );

        return response()->json([
            'message' => 'successfully delete',
            'laboratory' => $laboratory,
        ]);
    }

     // this method is for fetching all the laboratory services in the library
    public function lib_laboratory_index(){

        $laboratory = lib_laboratory::all();

        return response()->json($laboratory);
    }

}
