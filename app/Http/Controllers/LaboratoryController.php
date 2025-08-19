<?php

namespace App\Http\Controllers;

use App\Http\Requests\LaboratoryRequest;
use App\Models\Laboratory;
use App\Models\New_Consultation;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    //


    public function status(Request $request, $id)
    {
        // validate request
        $validated = $request->validate([
            'status' => 'required|in:Done,Returned,Pending'
        ]);

        // find laboratory record
        $lab = Laboratory::findOrFail($id);

        // update lab
        $lab->update($validated);

        // If lab is Returned, also update related consultation
        if ($lab->status === 'Returned' && $lab->new_consultation_id) {
            $consultation = New_Consultation::find($lab->new_consultation_id);

            if ($consultation) {
                $consultation->status = 'Returned';
                $consultation->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Laboratory status updated successfully.',
            'data' => $lab
        ]);
    }


    //     return response()->json([
    //         'message' => 'Laboratory stored successfully',
    //         'laboratory' => $lab
    //     ]);
    // }

    // public function store(LaboratoryRequest $request)
    // {
    //     $validated = $request->validated();

    //     // Create the laboratory record
    //     $lab = Laboratory::create($validated);




    //     return response()->json([
    //         'message' => 'Laboratory stored successfully',
    //         'laboratory' => $lab
    //     ]);
    // }

    public function store(LaboratoryRequest $request)
    {
        $validated = $request->validated();

        $labs = [];

        foreach ($validated['laboratories'] as $labData) {
            $labs[] = Laboratory::create([
                'transaction_id' => $validated['transaction_id'],
                'new_consultation_id' => $validated['new_consultation_id'] ?? null,
                'laboratory_type' => $labData['laboratory_type'],
                'amount' => $labData['amount'],
                'status' => $labData['status'] ?? 'Pending',
            ]);
        }

        return response()->json([
            'message' => 'Laboratories stored successfully',
            'laboratories' => $labs
        ]);
    }

}
