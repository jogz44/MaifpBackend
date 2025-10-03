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
use App\Http\Requests\lib_laboratory_examinationRequest;
use App\Http\Requests\lib_laboratoryRequest;
use App\Http\Requests\lib_radiologyRequest;
use App\Http\Requests\Mammogram_examination_Request;
use App\Http\Requests\UltraSoundRequest;
use App\Models\lab_examination_details;
use App\Models\lab_mammogram_details;
use App\Models\lab_radiology_details;
use App\Models\lab_ultrasound_details;
use App\Models\Lib_lab_examination;
use App\Models\lib_mammogram_examination;
use App\Models\lib_radiology;
use App\Models\lib_ultra_sound;
use App\Models\vw_patient_consultation_return;
use App\Models\vw_patient_laboratory;

class LaboratoryController extends Controller
{

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


    // this method for updating the status on the laboratory that is connected to the consultation for the patient
    public function Laboratory_status(Request $request)
    {
        // validate request
        $validated = $request->validate([
            'status' => 'required|in:Done,Returned,',
            'transaction_id' => 'required|exists:transaction,id',
        ]);

        $transaction = \App\Models\Transaction::with('consultation')
            ->findOrFail($validated['transaction_id']);

        $newConsultationId = $transaction->consultation
            ? $transaction->consultation->id
            : null;


        // Update or create the main laboratory record
        $lab = Laboratory::updateOrCreate(
            ['transaction_id' => $validated['transaction_id']], // condition
            [
                'status' => $validated['status'],
                'new_consultation_id' => $newConsultationId
            ]
        );

        // ✅ Also update all laboratory_details linked to this transaction
        $labDetails = Laboratory::where('transaction_id', $validated['transaction_id'])->get();

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


    public function store(LaboratoryRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

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

    public function destroy(Request $request)
    {

        $user = Auth::user();
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transaction,id',
            'type'           => 'required|string|in:radiology,examination,ultrasound,mammogram',
            'id'            => 'required|integer',

        ]);

        $transactionId = $validated['transaction_id'];
        $type = $validated['type'];
        $id   = $validated['id'];

        // Map type → model
        $models = [

            'radiology'  => \App\Models\lab_radiology_details::class,
            'examination'  => \App\Models\lab_examination_details::class,
            'ultrasound'   => \App\Models\lab_ultrasound_details::class,
            'mammogram'    => \App\Models\lab_mammogram_details::class,
        ];

        $model = $models[$type];

        $deletedCount = $model::where('transaction_id', $transactionId)
            ->where('id', $id)
            ->delete();


        // 📝 Activity Log for Delete
        return response()->json([
            'message' => $deletedCount > 0
                ? ucfirst($type) . " record deleted successfully"
                : "No matching record found to delete",
            'deleted' => $deletedCount,
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

    // lib laboratory examination
    public function getByTransaction_exam($transactionId)
    {
        $results = lab_examination_details::where('transaction_id', $transactionId)->get();

        return response()->json([
            'radiologies' => $results
        ]);
    }

    //  Store
    public function lib_lab_store(lib_laboratory_examinationRequest $request)
    {
        $validated = $request->validated();

        // Check if item_id already exists
        if (Lib_lab_examination::where('item_id', $validated['item_id'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Item ID already exists.',
            ], 422);
        }
        $lib = Lib_lab_examination::create($validated);
        return response()->json($lib);
    }

    //  Index
    public function lib_lab_index()
    {
        $lib = Lib_lab_examination::all();

        return response()->json($lib);
    }

    // Update
    public function lib_lab_update(lib_laboratory_examinationRequest $request, $lib_laboratory_examination_id)
    {
        $validated = $request->validated();

        // Check if item_id already exists for another record
        if (Lib_lab_examination::where('item_id', $validated['item_id'])
            ->where('id', '!=', $lib_laboratory_examination_id)
            ->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Item ID already exists for another record.',
            ], 422);
        }
        $lib = Lib_lab_examination::findOrFail($lib_laboratory_examination_id);
        $lib->update($validated);

        return response()->json($lib);
    }

    // Delete
    public function lib_lab_delete($lib_laboratory_examination_id)
    {
        $lib = Lib_lab_examination::findOrFail($lib_laboratory_examination_id);
        $lib->delete(); // ❌ no need to pass $lib, just call ->delete()

        return response()->json(['message' => 'Deleted successfully']);
    }

    //lib_radiology
    public function getByTransaction($transactionId)
    {
        $results = lab_radiology_details::where('transaction_id', $transactionId)->get();

        return response()->json([
            'radiologies' => $results
        ]);
    }

    public function lib_rad_index(){

        $lib = lib_radiology::all();

        return response()->json($lib);
    }

    public function lib_rad_store(lib_radiologyRequest $request)
    {
         $user = Auth::user();
        $validated = $request->validated();
        $lib = lib_radiology::create($validated);


        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($lib)
            ->withProperties([
                'ip'   => $request->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
            ])
            ->log(
                $lib
                    ? "Added new radiology service: {$lib->item_description} | Service Fee: ₱{$lib->service_fee} | Total: ₱{$lib->total_amount}"
                    : "Added new radiology service: Unknown"
            );


        return response()->json($lib);
    }

    public function lib_rad_update(lib_radiologyRequest $request ,$lib_rad_id)
    {

        $user =Auth::user();

        $validated = $request->validated();

        $lib = lib_radiology::findOrFail($lib_rad_id);

        $lib->update($validated);


        // Keep a copy of old values (for logging comparison)
        $oldValues = $lib->only(['item_description', 'service_fee', 'total_amount']);

        $lib->update($validated);

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($lib)
            ->withProperties([
                'ip'        => $request->ip(),
                'date'      => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'old'       => $oldValues,
                'new'       => $lib->only(['item_description', 'service_fee', 'total_amount']),
            ])
            ->log(
                "Updated radiology service: {$lib->item_description} | " .
                    "Service Fee: ₱{$lib->service_fee} | Total: ₱{$lib->total_amount}"
            );

        return response()->json($lib);
    }

    public function lib_rad_delete($lib_rad_id)
    {
            $user= Auth::user();
        $lib = lib_radiology::findOrFail($lib_rad_id);

        $lib->delete();

        // Capture old values before deleting
        $oldValues = $lib->only(['item_description', 'service_fee', 'total_amount']);

        // Log activity BEFORE deleting
        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($lib)
            ->withProperties([
                'ip'   => request()->ip(), // use helper function request()
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'old'  => $oldValues,
            ])
            ->log("Deleted radiology service: {$lib->item_description} | Service Fee: ₱{$lib->service_fee} | Total: ₱{$lib->total_amount}");

        // Delete the record
        $lib->delete();

        return response()->json([
            'success' => true,
            'message' => 'Radiology service deleted successfully',
            'data' => $oldValues
        ]);

    }


    //  lib ultra sound
    public function getByTransaction_ultrasound($transactionId)
    {
        $results = lab_ultrasound_details::where('transaction_id', $transactionId)->get();

        return response()->json([
            'radiologies' => $results
        ]);
    }

    public function lib_ultra_sound_index()
    {

        $lib = lib_ultra_sound::all();

        return response()->json($lib);
    }

    public function lib_ultra_sound_store(UltraSoundRequest $request)
    {
        $user = Auth::user();

        $validated = $request->validated();

        $lib = lib_ultra_sound::create($validated);

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($lib)
            ->withProperties([
                'ip'   => request()->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'new'  => $lib->only(['body_parts', 'rate', 'service_fee', 'total_amount']),
            ])
            ->log("Added new ultrasound service: {$lib->body_parts} | Rate: ₱{$lib->rate} | Service Fee: ₱{$lib->service_fee} | Total: ₱{$lib->total_amount}");

        return response()->json($lib);
    }

    public function lib_ultra_sound_update(UltraSoundRequest $request, $lib_ultra_sound_id)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $lib = lib_ultra_sound::findOrFail($lib_ultra_sound_id);

        $oldValues = $lib->only(['body_parts', 'rate', 'service_fee', 'total_amount']);

        $lib->update($validated);

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($lib)
            ->withProperties([
                'ip'   => request()->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'old'  => $oldValues,
                'new'  => $lib->only(['body_parts', 'rate', 'service_fee', 'total_amount']),
            ])
            ->log("Updated ultrasound service: {$lib->body_parts} | Rate: ₱{$lib->rate} | Service Fee: ₱{$lib->service_fee} | Total: ₱{$lib->total_amount}");

        return response()->json($lib);
    }

    public function lib_ultra_sound_delete($lib_ultra_sound_id)
    {
        $user = Auth::user();

        $lib = lib_ultra_sound::findOrFail($lib_ultra_sound_id);

        $oldValues = $lib->only(['body_parts', 'rate', 'service_fee', 'total_amount']);

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($lib)
            ->withProperties([
                'ip'   => request()->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'old'  => $oldValues,
            ])
            ->log("Deleted ultrasound service: {$lib->body_parts} | Rate: ₱{$lib->rate} | Service Fee: ₱{$lib->service_fee} | Total: ₱{$lib->total_amount}");

        $lib->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ultrasound service deleted successfully',
            'data'    => $oldValues
        ]);
    }


    // Lib Mammogram examination
    public function getByTransaction_mammogram($transactionId)
    {
        $results = lab_mammogram_details::where('transaction_id', $transactionId)->get();

        return response()->json([
            'radiologies' => $results
        ]);
    }

    public function lib_mammogram_index()
    {

        $lib = lib_mammogram_examination::all();

        return response()->json($lib);
    }

    public function lib_mammogram_store(Mammogram_examination_Request $request)
    {
        $user = Auth::user();
        $validated = $request->validated();
        $lib = lib_mammogram_examination::create($validated);

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($lib)
            ->withProperties([
                'ip'   => request()->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'new'  => $lib->only(['procedure', 'rate', 'service_fee', 'total_amount']),
            ])
            ->log("Added new mammogram service: {$lib->procedure} | Rate: ₱{$lib->rate} | Service Fee: ₱{$lib->service_fee} | Total: ₱{$lib->total_amount}");

        return response()->json($lib);
    }

    public function lib_mammogram_update(Mammogram_examination_Request $request, $lib_mammogram_id)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $lib = lib_mammogram_examination::findOrFail($lib_mammogram_id);

        $oldValues = $lib->only(['procedure', 'rate', 'service_fee', 'total_amount']);

        $lib->update($validated);

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($lib)
            ->withProperties([
                'ip'   => request()->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'old'  => $oldValues,
                'new'  => $lib->only(['procedure', 'rate', 'service_fee', 'total_amount']),
            ])
            ->log("Updated mammogram service: {$lib->procedure} | Rate: ₱{$lib->rate} | Service Fee: ₱{$lib->service_fee} | Total: ₱{$lib->total_amount}");


        return response()->json($lib);
    }

    public function lib_mammogram_delete($lib_mammogram_id)
    {
        $user = Auth::user();

        $lib = lib_mammogram_examination::findOrFail($lib_mammogram_id);
        $oldValues = $lib->only(['procedure', 'rate', 'service_fee', 'total_amount']);

        activity($user->first_name . ' ' . $user->last_name)
            ->causedBy($user)
            ->performedOn($lib)
            ->withProperties([
                'ip'   => request()->ip(),
                'date' => now('Asia/Manila')->format('Y-m-d h:i:s A'),
                'old'  => $oldValues,
            ])
            ->log("Deleted mammogram service: {$lib->procedure} | Rate: ₱{$lib->rate} | Service Fee: ₱{$lib->service_fee} | Total: ₱{$lib->total_amount}");

        $lib->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mammogram service deleted successfully',
            'data'    => $oldValues
        ]);
    }


    //fetch all that of the patient of this laboratory
    public function Laboratory_transaction($transactionId)
    {
        $transaction = Transaction::with([
            'radiologies_details',
            'examination_details',
            'ultrasound_details',
            'mammogram_details'
        ])->find($transactionId);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Transform the data
        $data = [
            'transaction_id' => $transaction->id,
            'radiologies' => $transaction->radiologies_details->map(function ($item) {
                return [
                    'item_description' => $item->item_description,
                    'selling_price'    => $item->selling_price,
                    'service_fee'      => $item->service_fee,
                    'total_amount'     => $item->total_amount,
                ];
            }),
            'examination' => $transaction->examination_details->map(function ($item) {
                return [
                    'item_id'          => $item->item_id,
                    'item_description' => $item->item_description,
                    'selling_price'    => $item->selling_price,
                    'service_fee'      => $item->service_fee,
                    'total_amount'     => $item->total_amount,
                ];
            }),
            'ultrasound' => $transaction->ultrasound_details->map(function ($item) {
                return [
                    'body_parts'   => $item->body_parts,
                    'rate'         => $item->rate,
                    'service_fee'  => $item->service_fee,
                    'total_amount' => $item->total_amount,
                ];
            }),
            'mammogram' => $transaction->mammogram_details->map(function ($item) {
                return [
                    'procedure'    => $item->procedure,
                    'rate'         => $item->rate,
                    'service_fee'  => $item->service_fee,
                    'total_amount' => $item->total_amount,
                ];
            }),
        ];

        return response()->json($data);
    }
}
