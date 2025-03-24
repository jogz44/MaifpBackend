<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Models\System_users;

class SystemUserController extends Controller
{
    //
    public function index()
    {
        try {

            $System_users = System_users::orderBy('id', 'desc')
                ->get();
            return response()->json(['success' => true, 'users' =>  $System_users]);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
            //throw $th;
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $System_users = System_users::find($id)
                ->get();
            return response()->json(['success' => true, 'user' =>  $System_users]);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
            //throw $th;
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {

        try {

            $validationInput = $request->validate(
                [
                'po_no' => 'required|string|max:50',
                'brand_name' => 'required|string|max:100',
                'generic_name' => 'required|string|max:100',
                'dosage_form' => 'nullable|string|max:50',
                'dosage' => 'required|string|max:50',
                'category' => 'nullable|string|max:50',
                'unit' => 'required|string|max:50',
                'quantity' => 'required|numeric|min:1',
                'expiration_date' => 'required|date|after:today',
                'user_id' => 'required|exists:users,id',
                ]
            );

            $System_users = System_users::create($validationInput);
            return response()->json([
                'success' => true,
                'users' =>  $System_users
            ]);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
            //throw $th;
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id) {
        try {
            $user = System_users::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'user not found'], 404);
            }

            $validationInput = $request->validate(
                [
                    'po_no' => 'required|string|max:50',
                    'brand_name' => 'required|string|max:100',
                    'generic_name' => 'required|string|max:100',
                    'dosage_form' => 'nullable|string|max:50',
                    'dosage' => 'required|string|max:50',
                    'category' => 'nullable|string|max:50',
                    'unit' => 'required|string|max:50',
                    'quantity' => 'required|numeric|min:1',
                    'expiration_date' => 'required|date|after:today',
                    'user_id' => 'required|exists:users,id',
                ]
            );

            $user->update($validationInput);
            return response()->json([
                'success' => true,
                'user' =>  $user
            ]);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
            //throw $th;
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }

    }




    public function destroy($id) {
        try {
            $user = System_users::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'user not found'], 404);
            }
            $user->delete();
            return response()->json([
                'success' => true,
                'user' =>  $user
            ],200);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
            //throw $th;
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
