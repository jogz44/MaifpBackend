<?php

namespace App\Services;

use App\Models\Laboratory;
use App\Models\New_Consultation;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaboratoryService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    // fetch patients on laboratory
    // public function laboratory()
    // {
    //     $lab = DB::table('transactions as t')
    //         ->join('patient as p', 'p.id', '=', 't.patient_id')

    //         ->leftJoin('vital as v', 'v.transaction_id', '=', 't.id')
    //         ->leftJoin('new_consultation as c', 'c.transaction_id', '=', 't.id')
    //         ->leftJoin('laboratory as l', 'l.transaction_id', '=', 't.id')
    //         ->whereIn('t.status', ['Qualified', 'Pending'])

    //         ->where(function ($q) {
    //             $q->whereIn('t.transaction_type', ['Laboratory','Consultation']) // transaction_type  lab and consultation
    //                 ->orWhere('c.status', 'Processing');
    //         })

    //         ->whereNotExists(function ($query) {
    //             $query->select(DB::raw(1))
    //                 ->from('laboratory as l2') // if meron na status ang transaction  done,returned,pending hinde na siya e sama
    //                 ->whereColumn('l2.transaction_id', 't.id')
    //                 ->whereIn('l2.status', ['Done', 'Pending']); // returned and Done
    //         })
    //         ->select(
    //             'p.id',
    //             'p.firstname',
    //             'p.lastname',
    //             'p.middlename',
    //             'p.ext',
    //             'p.birthdate',
    //             'p.contact_number',
    //             'p.age',
    //             'p.gender',
    //             'p.is_not_tagum',
    //             'p.street',
    //             'p.purok',
    //             'p.barangay',
    //             'p.city',
    //             'p.province',
    //             'p.category',
    //             'p.is_pwd',
    //             'p.is_solo',
    //             'p.user_id',
    //             'p.created_at as patient_created_at',
    //             'p.updated_at as patient_updated_at',

    //             't.id',
    //             't.transaction_number',
    //             't.transaction_type',
    //             't.status',
    //             't.transaction_date',
    //             't.transaction_mode',
    //             't.purpose',
    //             't.created_at as transaction_created_at',
    //             't.updated_at as transaction_updated_at',
    //             't.representative_id',

    //             'c.id as consultation_id',
    //             'c.status as consultation_status',

    //             'v.id as vital_id',
    //             'v.height',
    //             'v.weight',
    //             'v.bmi',
    //             'v.pulse_rate',
    //             'v.temperature',
    //             'v.sp02',
    //             'v.heart_rate',
    //             'v.blood_pressure',
    //             'v.respiratory_rate',
    //             'v.medicine',
    //             'v.LMP'
    //         )
    //         ->get();


    //     return $lab;
    // }

    public function laboratory()
    {
        $patientLab = Patient::with([
            'transaction.consultation',
            'transaction.vital',
            'transaction.laboratories',
            'transaction.medication'
        ])
            ->whereHas('transaction', function ($q) {

                $q->whereIn('status', ['Qualified', 'Pending'])

                    ->where(function ($q2) {
                        $q2->whereIn('transaction_type', ['Laboratory', 'Consultation','Medication'])
                            ->orWhereHas('consultation', function ($c) {
                                $c->where('status', ['Returned',]);
                            });
                            // ->orWhereHas('laboratories', function ($l) {
                            //     $l->where('status', ['Done']);
                            // });
                    });
                // ->whereDoesntHave('consultation', function ($med) {
                //     $med->whereIn('status', ['Medication']);
                // })
                //   ->whereDoesntHave('medication', function ($med) {
                //             $med->whereIn('status', ['Medication']);
                //         })
                //     ->whereDoesntHave('laboratories', function ($lab) {
                //         $lab->whereIn('status', ['Done', 'Pending']);
                //     });
            })
            ->get();


        return $patientLab;
    }


    // this method for updating the status on the laboratory that is connected to the consultation for the patient
    // public function status($validated)
    // {

    //     $transaction = \App\Models\Transaction::with('consultation')
    //         ->findOrFail($validated['transaction_id']);

    //     $newConsultationId = $transaction->consultation
    //         ? $transaction->consultation->id
    //         : null;


    //     // add logic if the patient dont have consultation_id but he will update done
    //     // he must be store on the new_consultation  the value consultation_time, consultation_date, amount, status



    //     $date = Carbon::now()->format('y/m/d');
    //     $time = Carbon::now()->format('H:i:s A');
    //     $amount = 0.00;

    //     $store_consultation = New_Consultation::updateOrcreate(
    //         [
    //             'transaction_id' => $validated['transaction_id'],
    //             'patient_id' => patient_id,
    //             'consultation_date' => $date,
    //             'consultation_time' => $time,
    //             'amount' => $amount,

    //         ]

    //         );





    //     // Update or create the main laboratory record
    //     $lab = Laboratory::updateOrCreate(
    //         ['transaction_id' => $validated['transaction_id']], // condition
    //         [
    //             'status' => $validated['status'],
    //             'new_consultation_id' => $newConsultationId
    //         ]
    //     );

    //     // ✅ Also update all laboratory_details linked to this transaction
    //     $labDetails = Laboratory::where('transaction_id', $validated['transaction_id'])->get();

    //     foreach ($labDetails as $detail) {
    //         // If Returned, update related consultation
    //         if ($validated['status'] === 'Returned' && $detail->new_consultation_id) {
    //             $consultation = New_Consultation::find($detail->new_consultation_id);
    //             if ($consultation) {
    //                 $consultation->status = 'Returned';
    //                 $consultation->save();
    //             }
    //         }
    //     }




    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Laboratory status under this transaction updated successfully.',
    //         'data' => $lab
    //     ]);
    // }

    // public function status($validated)
    // {
    //     $transaction = \App\Models\Transaction::with('consultation')
    //         ->findOrFail($validated['transaction_id']);

    //     // ✅ Only apply this logic if transaction_type is Consultation
    //     if ($transaction->transaction_type === 'Consultation') {

    //         // Check if transaction_id already exists in new_consultation
    //         $existingConsultation = New_Consultation::where(
    //             'transaction_id',
    //             $validated['transaction_id']
    //         )->first();

    //         // If NOT existing, create new consultation
    //         if (!$existingConsultation) {

    //             $date = Carbon::now()->format('Y-m-d');
    //             $time = Carbon::now()->format('H:i:s');
    //             $amount = 0.00;

    //             $newConsultation = New_Consultation::create([
    //                 'transaction_id'    => $validated['transaction_id'],
    //                 'patient_id'        => $transaction->patient_id, // ✅ get from transaction
    //                 'consultation_date' => $date,
    //                 'consultation_time' => $time,
    //                 'amount'            => $amount,
    //                 'status'            => $validated['status'],
    //             ]);

    //             $newConsultationId = $newConsultation->id;
    //         } else {
    //             // If already exists, use existing id
    //             $newConsultationId = $existingConsultation->id;
    //         }
    //     } else {
    //         // If NOT Consultation type
    //         $newConsultationId = null;
    //     }

    //     // ✅ Update or create the main laboratory record
    //     $lab = Laboratory::updateOrCreate(
    //         ['transaction_id' => $validated['transaction_id']],
    //         [
    //             'status' => $validated['status'],
    //             'new_consultation_id' => $newConsultationId
    //         ]
    //     );

    //     // ✅ If Returned, update related consultation
    //     if ($validated['status'] === 'Returned' && $newConsultationId) {
    //         $consultation = New_Consultation::find($newConsultationId);
    //         if ($consultation) {
    //             $consultation->status = 'Returned';
    //             $consultation->save();
    //         }
    //     }





    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Laboratory status under this transaction updated successfully.',
    //         'data' => $lab
    //     ]);
    // }
    public function status($validated)
    {
        $transaction = \App\Models\Transaction::with('consultation')
            ->findOrFail($validated['transaction_id']);

        $newConsultationId = null;

        // ✅ Only apply this logic if transaction_type is Consultation
        if ($transaction->transaction_type === 'Consultation') {

            $existingConsultation = New_Consultation::where(
                'transaction_id',
                $validated['transaction_id']
            )->first();

            // ✅ If NOT existing, create new consultation
            if (!$existingConsultation) {

                $date = Carbon::now()->format('Y-m-d');
                $time = Carbon::now()->format('H:i:s');
                $amount = 0.00;

                $newConsultation = New_Consultation::create([
                    'transaction_id'    => $validated['transaction_id'],
                    'patient_id'        => $transaction->patient_id,
                    'consultation_date' => $date,
                    'consultation_time' => $time,
                    'amount'            => $amount,
                    'status'            => $validated['status'],
                ]);

                $newConsultationId = $newConsultation->id;
            } else {

                $newConsultationId = $existingConsultation->id;

                // 🔥 NEW LOGIC HERE
                // If updating to Done and current consultation is Returned
                if (
                    $validated['status'] === 'Done' &&
                    $existingConsultation->status === 'Returned'
                ) {
                    $existingConsultation->status = 'Done';
                    $existingConsultation->save();
                }
            }
        }

        // ✅ Update or create the main laboratory record
        $lab = Laboratory::updateOrCreate(
            ['transaction_id' => $validated['transaction_id']],
            [
                'status' => $validated['status'],
                'new_consultation_id' => $newConsultationId
            ]
        );

        // ✅ If Returned, update consultation also
        if ($validated['status'] === 'Returned' && $newConsultationId) {
            $consultation = New_Consultation::find($newConsultationId);
            if ($consultation) {
                $consultation->status = 'Returned';
                $consultation->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Laboratory status under this transaction updated successfully.',
            'data' => $lab
        ]);
    }
}
