<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\lib_laboratory;
use App\Models\New_Consultation;
use Illuminate\Support\Facades\DB;
use App\Models\Laboratories_details;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Validated;
use App\Http\Requests\LaboratoryRequest;
use App\Http\Requests\lib_laboratoryRequest;
use App\Models\vw_patient_consultation_return;
use App\Models\vw_patient_laboratory;

class LaboratoryController extends Controller
{
    // this method is for laboratory will fetch the patient need to laboratory
    // public function qualifiedTransactionsLaboratory()
    // {
    //     try {
    //         $transactions = Transaction::where('status', 'qualified')
    //             ->where(function ($query) {
    //                 $query->where('transaction_type', 'Laboratory')
    //                     ->orWhereHas('consultation', function ($q) {
    //                         $q->where('status', 'Processing');
    //                     });
    //             })
    //             //  Exclude transactions that already have laboratories with status = 'Done'
    //             ->whereDoesntHave('laboratories', function ($lab) {
    //             $lab->whereIn('status', ['Done', 'Returned', 'Pending']);
    //             })
    //             // ->whereDate('transaction_date', now()->toDateString()) // ✅ per transaction date (today)
    //             ->with([
    //                 'patient',
    //                 'vital',       // fetch vitals of the transaction
    //                 'consultation',
    //                 'laboratories' // fetch laboratories
    //             ])
    //             ->get()
    //             ->groupBy('patient_id')
    //             ->map(function ($group) {
    //                 $patient = $group->first()->patient;

    //                 // attach transactions to patient
    //                 $patient->transaction = $group->map(function ($transaction) {
    //                     return collect($transaction)->except('patient');
    //                 })->values();

    //                 return $patient;
    //             })
    //             ->values();

    //         return response()->json($transactions);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch qualified transactions.',
    //             'error' => $th->getMessage()
    //         ], 500);
    //     }
    // }
    // public function qualifiedTransactionsLaboratory()
    // {
    //     try {
    //         $rows = DB::table('transaction as t')
    //             ->join('patient as p', 'p.id', '=', 't.patient_id')
    //             ->leftJoin('vital as v', 'v.transaction_id', '=', 't.id')
    //             ->leftJoin('new_consultation as c', 'c.transaction_id', '=', 't.id')
    //             ->leftJoin('laboratory as l', 'l.transaction_id', '=', 't.id')
    //             ->select(
    //                 // Patient fields
    //                 'p.id as patient_id',
    //                 'p.firstname',
    //                 'p.lastname',
    //                 'p.middlename',
    //                 'p.ext',
    //                 'p.birthdate',
    //                 'p.contact_number',
    //                 'p.age',
    //                 'p.gender',
    //                 'p.is_not_tagum',
    //                 'p.street',
    //                 'p.purok',
    //                 'p.barangay',
    //                 'p.city',
    //                 'p.province',
    //                 'p.category',
    //                 'p.is_pwd',
    //                 'p.is_solo',
    //                 'p.user_id',
    //                 'p.created_at as patient_created_at',
    //                 'p.updated_at as patient_updated_at',

    //                 // Transaction fields
    //                 't.id as transaction_id',
    //                 't.transaction_number',
    //                 't.transaction_type',
    //                 't.status as transaction_status',
    //                 't.transaction_date',
    //                 't.transaction_mode',
    //                 't.purpose',
    //                 't.created_at as transaction_created_at',
    //                 't.updated_at as transaction_updated_at',
    //                 't.representative_id',

    //                 // Consultation fields
    //                 'c.id as consultation_id',
    //                 'c.status as consultation_status',

    //                 // Vital fields
    //                 'v.id as vital_id',
    //                 'v.height',
    //                 'v.weight',
    //                 'v.bmi',
    //                 'v.pulse_rate',
    //                 'v.temperature',
    //                 'v.sp02',
    //                 'v.heart_rate',
    //                 'v.blood_pressure',
    //                 'v.respiratory_rate',
    //                 'v.medicine',
    //                 'v.LMP',

    //                 // Laboratory fields
    //                 'l.id as laboratory_id',
    //                 'l.status as laboratory_status',
    //                 'l.created_at as laboratory_created_at',
    //                 'l.updated_at as laboratory_updated_at'
    //             )
    //             ->where('t.status', 'qualified')
    //             ->where(function ($query) {
    //                 $query->where('t.transaction_type', 'Laboratory')
    //                     ->orWhere('c.status', 'Processing');
    //             })
    //             // exclude transactions that already have labs with status Done/Returned/Pending
    //             ->whereNotExists(function ($sub) {
    //                 $sub->select(DB::raw(1))
    //                     ->from('laboratory as l2')
    //                     ->whereRaw('l2.transaction_id = t.id')
    //                     ->whereIn('l2.status', ['Done', 'Returned', 'Pending']);
    //             })
    //             // ->whereDate('t.transaction_date', now()->toDateString()) // ✅ if you want only today's
    //             ->orderBy('p.id')
    //             ->get();

    //         // Group by patient
    //         $grouped = $rows->groupBy('patient_id')->map(function ($group) {
    //             $first = $group->first();

    //             return [
    //                 'id'             => $first->patient_id,
    //                 'firstname'      => $first->firstname,
    //                 'lastname'       => $first->lastname,
    //                 'middlename'     => $first->middlename,
    //                 'ext'            => $first->ext,
    //                 'birthdate'      => $first->birthdate,
    //                 'contact_number' => $first->contact_number,
    //                 'age'            => $first->age,
    //                 'gender'         => $first->gender,
    //                 'is_not_tagum'   => $first->is_not_tagum,
    //                 'street'         => $first->street,
    //                 'purok'          => $first->purok,
    //                 'barangay'       => $first->barangay,
    //                 'city'           => $first->city,
    //                 'province'       => $first->province,
    //                 'category'       => $first->category,
    //                 'is_pwd'         => $first->is_pwd,
    //                 'is_solo'        => $first->is_solo,
    //                 'user_id'        => $first->user_id,
    //                 'created_at'     => $first->patient_created_at,
    //                 'updated_at'     => $first->patient_updated_at,

    //                 'transaction' => $group->map(function ($t) {
    //                     return [
    //                         'id'                 => $t->transaction_id,
    //                         'transaction_number' => $t->transaction_number,
    //                         'transaction_type'   => $t->transaction_type,
    //                         'status'             => $t->transaction_status,
    //                         'transaction_date'   => $t->transaction_date,
    //                         'transaction_mode'   => $t->transaction_mode,
    //                         'purpose'            => $t->purpose,
    //                         'created_at'         => $t->transaction_created_at,
    //                         'updated_at'         => $t->transaction_updated_at,
    //                         'representative_id'  => $t->representative_id,

    //                         'consultation' => $t->consultation_id ? [
    //                             'id'     => $t->consultation_id,
    //                             'status' => $t->consultation_status,
    //                         ] : null,

    //                         'vital' => $t->vital_id ? [
    //                             'id'              => $t->vital_id,
    //                             'height'          => $t->height,
    //                             'weight'          => $t->weight,
    //                             'bmi'             => $t->bmi,
    //                             'pulse_rate'      => $t->pulse_rate,
    //                             'temperature'     => $t->temperature,
    //                             'sp02'            => $t->sp02,
    //                             'heart_rate'      => $t->heart_rate,
    //                             'blood_pressure'  => $t->blood_pressure,
    //                             'respiratory_rate' => $t->respiratory_rate,
    //                             'medicine'        => $t->medicine,
    //                             'LMP'             => $t->LMP,
    //                         ] : null,

    //                         'laboratory' => $t->laboratory_id ? [
    //                             'id'         => $t->laboratory_id,
    //                             'status'     => $t->laboratory_status,
    //                             'created_at' => $t->laboratory_created_at,
    //                             'updated_at' => $t->laboratory_updated_at,
    //                         ] : null,
    //                     ];
    //                 })->values()
    //             ];
    //         })->values();

    //         return response()->json($grouped);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch qualified laboratory transactions.',
    //             'error'   => $th->getMessage()
    //         ], 500);
    //     }
    // }
    public function qualifiedTransactionsLaboratory()
    {
        try {
            $records = vw_patient_laboratory::all();

            $grouped = $records->groupBy('patient_id')->map(function ($items) {
                $first = $items->first();

                return [
                    'id'             => $first->patient_id,
                    'firstname'      => $first->firstname,
                    'lastname'       => $first->lastname,
                    'middlename'     => $first->middlename,
                    'ext'            => $first->ext,
                    'birthdate'      => $first->birthdate,
                    'contact_number' => $first->contact_number,
                    'age'            => $first->age,
                    'gender'         => $first->gender,
                    'is_not_tagum'   => $first->is_not_tagum,
                    'street'         => $first->street,
                    'purok'          => $first->purok,
                    'barangay'       => $first->barangay,
                    'city'           => $first->city,
                    'province'       => $first->province,
                    'category'       => $first->category,
                    'is_pwd'         => $first->is_pwd,
                    'is_solo'        => $first->is_solo,
                    'user_id'        => $first->user_id,
                    'created_at'     => $first->patient_created_at,
                    'updated_at'     => $first->patient_updated_at,

                    'transaction'    => $items->map(function ($row) {
                        return [
                            'id'                 => $row->transaction_id,
                            'transaction_number' => $row->transaction_number,
                            'transaction_type'   => $row->transaction_type,
                            'status'             => $row->transaction_status,
                            'transaction_date'   => $row->transaction_date,
                            'transaction_mode'   => $row->transaction_mode,
                            'purpose'            => $row->purpose,
                            'created_at'         => $row->transaction_created_at,
                            'updated_at'         => $row->transaction_updated_at,
                            'representative_id'  => $row->representative_id,

                            'consultation' => $row->consultation_id ? [
                                'id'     => $row->consultation_id,
                                'status' => $row->consultation_status,
                            ] : null,

                            'vital' => $row->vital_id ? [
                                'id'               => $row->vital_id,
                                'height'           => $row->height,
                                'weight'           => $row->weight,
                                'bmi'              => $row->bmi,
                                'pulse_rate'       => $row->pulse_rate,
                                'temperature'      => $row->temperature,
                                'sp02'             => $row->sp02,
                                'heart_rate'       => $row->heart_rate,
                                'blood_pressure'   => $row->blood_pressure,
                                'respiratory_rate' => $row->respiratory_rate,
                                'medicine'         => $row->medicine,
                                'LMP'              => $row->LMP,
                            ] : null,

                            'laboratory' => null, // you can extend view if you need lab details
                        ];
                    })->values()
                ];
            })->values();

            return response()->json($grouped);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch laboratory records.',
                'error'   => $th->getMessage()
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
