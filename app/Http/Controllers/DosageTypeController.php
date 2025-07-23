<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;


use Illuminate\Http\Request;
use App\Models\libDosageType as DosageType;

class DosageTypeController extends Controller
{
    //
    public function show($id)
    {
        try {
            $dosage = DosageType::findorFail($id);
            if (!$dosage) {
                return response()->json(['success' => false, 'message' => 'Dosage type not found'], 404);
            }
            return response()->json(['success' => true, 'dosagetype' => $dosage], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch Dosage type', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'type' => 'required|string',

            ]);

            $dosage = DosageType::create($validatedData);
            return response()->json(['success' => true, 'dosagetype' => $dosage, 'message' => 'Dosage type entry successful'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create Dosage type', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $dosage = DosageType::find($id);
            if (!$dosage) {
                return response()->json(['message' => 'Dosage type not found'], 404);
            }

            $validatedData = $request->validate([
                'type' => 'required|string',

            ]);

            $dosage->update($validatedData);
            return response()->json(['success' => true, 'dosagetype' => $dosage, 'message' => 'Dosage type updated successfully'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update Dosage type', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $dosage = DosageType::find($id);
            if (!$dosage) {
                return response()->json(['message' => 'Dosage type not found'], 404);
            }

            $dosage->delete();
            return response()->json(['message' => 'Dosage type deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete Dosage type', 'error' => $e->getMessage()], 500);
        }
    }

    public function getDosageTypes()
    {
        try {
            //code...
            $dosage = DosageType::orderby('id', 'asc')->get();


            if ($dosage->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No Dosage type found'], 404);
            }
            return response()->json(['success' => true, 'dosagetypes' => $dosage], 200);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch Dosage type', 'error' => $th->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch Dosage type', 'error' => $e->getMessage()], 500);
        }


    }


    public function removeDosageType(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:lib_dosagetype,id',
        ]);

        try {
            // Optional: you already validated with "exists", so this can be skipped
            $deleted = DB::table('lib_dosagetype')->where('id', $request->id)->delete();

            if ($deleted === 0) {
                return response()->json(['success' => false, 'message' => 'Dosage type not found'], 404);
            }
            // If the deletion was successful, return a success response
            return response()->json(['success' => true, 'message' => 'Dosage type deleted successfully'], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Dosage type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
