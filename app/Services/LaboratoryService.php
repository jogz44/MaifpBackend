<?php

namespace App\Services;

use App\Models\lab_examination_details;
use App\Models\lab_mammogram_details;
use App\Models\lab_radiology_details;
use App\Models\lab_ultrasound_details;
use App\Models\Laboratory;
use App\Models\New_Consultation;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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

    // fetch the patient on laboratory
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


   // update the transaction status
    public function status($validated,)
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

    // store the laboratory services
    public function store($validated,$request)
    {
        $user = Auth::user();


        $transaction = \App\Models\Transaction::with('consultation')
            ->findOrFail($validated['transaction_id']);

        $newConsultationId = $transaction->consultation
            ? $transaction->consultation->id
            : null;

        $savedRecords = [];

        // ✅ Save Radiologies
        if (!empty($validated['radiologies'])) {
            foreach ($validated['radiologies'] as $radData) {
                $savedRecords['radiologies'][] = lab_radiology_details::create([
                    'transaction_id'      => $validated['transaction_id'],
                    'new_consultation_id' => $newConsultationId,
                    'item_description'    => $radData['item_description'],
                    'selling_price'       => $radData['selling_price'],
                    'service_fee'         => $radData['service_fee'],
                    'total_amount'        => $radData['total_amount'],
                ]);
            }
        }

        // ✅ Save Examinations
        if (!empty($validated['examination'])) {
            foreach ($validated['examination'] as $examData) {
                $savedRecords['examination'][] = lab_examination_details::create([
                    'transaction_id'      => $validated['transaction_id'],
                    'new_consultation_id' => $newConsultationId,
                    'item_id'             => $examData['item_id'],
                    'item_description'    => $examData['item_description'],
                    'selling_price'       => $examData['selling_price'],
                    'service_fee'         => $examData['service_fee'],
                    'total_amount'        => $examData['total_amount'],
                ]);
            }
        }

        // ✅ Save ultrasound
        if (!empty($validated['ultrasound'])) {
            foreach ($validated['ultrasound'] as $ultraData) {
                $savedRecords['ultrasound'][] = lab_ultrasound_details::create([
                    'transaction_id'      => $validated['transaction_id'],
                    'new_consultation_id' => $newConsultationId,
                    'body_parts'    => $ultraData['body_parts'],
                    'rate'       => $ultraData['rate'],
                    'service_fee'         => $ultraData['service_fee'],
                    'total_amount'        => $ultraData['total_amount'],
                ]);
            }
        }

        // ✅ Save mammogram
        if (!empty($validated['mammogram'])) {
            foreach ($validated['mammogram'] as $mammogramData) {
                $savedRecords['mammogram'][] = lab_mammogram_details::create([
                    'transaction_id'      => $validated['transaction_id'],
                    'new_consultation_id' => $newConsultationId,
                    'procedure'    => $mammogramData['procedure'],
                    'rate'       => $mammogramData['rate'],
                    'service_fee'         => $mammogramData['service_fee'],
                    'total_amount'        => $mammogramData['total_amount'],
                ]);
            }
        }

        // Prepare log details
        // Prepare log details
        $logEntries = [];

        foreach ($savedRecords as $type => $records) {
            foreach ($records as $record) {
                switch ($type) {
                    case 'radiologies':
                        $logEntries[] = "Radiology: {$record->item_description} (₱{$record->total_amount})";
                        break;
                    case 'examination':
                        $logEntries[] = "Examination: {$record->item_description} (₱{$record->total_amount})";
                        break;
                    case 'ultrasound':
                        $logEntries[] = "Ultrasound: {$record->body_parts} (₱{$record->total_amount})";
                        break;
                    case 'mammogram':
                        $logEntries[] = "Mammogram: {$record->procedure} (₱{$record->total_amount})";
                        break;
                    default:
                        $logEntries[] = "Unknown Service (₱{$record->total_amount})";
                }
            }
        }

        $labDetails = implode(', ', $logEntries);
        // ✅ Then broadcast the fresh counts AFTER the DB has changed


        // Log activity
        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($transaction) // better to log on the transaction
            ->withProperties([
                'ip'   => $request->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'labs' => $savedRecords
            ])
            ->log("Added new services: {$labDetails}");


        return response()->json([
            'message' => 'Records stored successfully',
            'data'    => $savedRecords,
        ]);
    }
}
