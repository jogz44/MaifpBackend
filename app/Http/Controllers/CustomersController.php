<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Models\Customers;


class CustomersController extends Controller
{
    //

    public function index()
    {
        try {

            $customers = Customers::orderBy('id', 'desc')
                ->get();
            return response()->json(['success' => true, 'customers' =>  $customers]);
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
                'errors' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {

        try {
            $customers = Customers::where('id',$id)
                ->get();
            return response()->json( $customers, 200);
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
                'errors' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validationInput = $request->validate(
                [
                    'firstname' => 'required|string|max:255',
                    'lastname' => 'required|string|max:255',
                    'middlename' => 'nullable|string|max:255',
                    'ext' => 'nullable|string|max:255',
                    'birthdate' => 'required|date',
                    'contact_number' => 'nullable|string|max:11',
                    'age' => 'integer',
                    'gender' => 'required|string|max:11',
                    'is_not_tagum' => 'boolean',
                    'street' => 'nullable|string|max:255',
                    'purok'  => 'nullable|string|max:255',
                    'barangay' => 'required|string|max:255',
                    'city' => 'nullable|string|max:255',
                    'province' => 'nullable|string|max:255',
                    'category' => 'required|in:Child,Adult,Senior',
                    'is_pwd' => 'boolean',
                    'is_solo' => 'boolean',
                    'user_id' => 'required|exists:users,id'
                ]
            );

            $customers = Customers::create($validationInput);
            return response()->json([
                'success' => true,
                'customers' =>  $customers
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
                'errors' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id) {
        try {
            $customer = Customers::where('id',$id)->first();
            if (!$customer) {
                return response()->json(['success' => false, 'message' => 'Client not found'], 404);
            }

            $validationInput = $request->validate(
                [
                    'firstname' => 'required|string|max:255',
                    'lastname' => 'required|string|max:255',
                    'middlename' => 'nullable|string|max:255',
                    'ext' => 'nullable|string|max:255',
                    'birthdate' => 'required|date',
                    'contact_number' => 'nullable|string|max:11',
                    'age' => 'integer',
                    'gender' => 'required|string|max:11',
                    'is_not_tagum' => 'boolean',
                    'street' => 'nullable|string|max:255',
                    'purok'  => 'required|string|max:255',
                    'barangay' => 'required|string|max:255',
                    'city' => 'nullable|string|max:255',
                    'province' => 'nullable|string|max:255',
                    'category' => 'required|in:Child,Adult,Senior',
                    'is_pwd' => 'boolean',
                    'is_solo' => 'boolean',
                    'user_id' => 'required|exists:users,id'
                ]
            );

            $customer->update($validationInput);
            return response()->json([
                'success' => true,
                'customers' =>  $customer
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
                'errors' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }

    }

    public function destroy($id) {
        try {
            $customer = Customers::where('id',$id);
            if (!$customer) {
                return response()->json(['success' => false, 'message' => 'Client not found'], 404);
            }
            $customer->delete();
            return response()->json([
                'success' => true,
                'customers' =>  $customer,
                'message'=> 'Customer information deleted'
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
                'errors' => $qe->getMessage()
            ], 500);
            //throw $th;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }
    }
}
