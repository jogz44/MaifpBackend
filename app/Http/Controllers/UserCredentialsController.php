<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserCredentials;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class UserCredentialsController extends Controller
{
    //


    public function index()
    {
        try {
            //code...
            $credentials = UserCredentials::with('user')->get();
            if ($credentials->isEmpty()) {
                return response()->json(['success'=> false, 'message'=> 'No user credentials found.'], 404);
            }
            return response()->json(['success'=> true, 'credential'=> $credentials], 200);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
        } catch (\Throwable $th) {
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
            //code...
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
                'module' => 'required|string',
                'view' => 'boolean',
                'add' => 'boolean',
                'edit' => 'boolean',
                'delete' => 'boolean',
                'export' => 'boolean',
            ]);

            $credential = UserCredentials::create($data);

            return response()->json(['success'=> true, 'credential'=> $credential, 'message'=> 'User credential created successfully.'], 201);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
        } catch (\Throwable $th) {
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
            $credentials= UserCredentials::findOrFail($id);
            return response()->json(['success'=> true, 'credential'=> $credentials,], 200);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $credential = UserCredentials::findOrFail($id);

            $data = $request->validate([
                'view' => 'boolean',
                'add' => 'boolean',
                'edit' => 'boolean',
                'delete' => 'boolean',
                'export' => 'boolean',
            ]);

            $credential->update($data);
            return response()->json(['success'=> true, 'credential'=> $credential, 'message'=> 'User credential updated successfully.'], 201);

        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $credential = UserCredentials::findOrFail($id);
            $credential->delete();
            return response()->json(['success'=> true, 'credential'=> $credential, 'message'=> 'User credential deleted successfully.'], 201);

        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $qe->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
