<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class SystemUserController extends Controller
{
    //
    public function index()
    {
        try {

            $System_users = User::orderBy('id', 'desc')
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
            $System_users = User::where('id', $id)
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
            $validationInput = $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'middle_name' => 'nullable|string|max:100',
                'position' => 'required|string|max:100',
                'office' => 'required|string|max:100',
                'username' => 'required|string|max:100|unique:users,username',
                'password' => 'required|string|max:16',
            ]);

            // No need to hash password manually here
            $System_users = User::create($validationInput);

            return response()->json([
                'success' => true,
                'user' => $System_users
            ]);
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
        // try {
        //     $user = User::find($id);
        //     if (!$user) {
        //         return response()->json(['success' => false, 'message' => 'user not found'], 404);
        //     }

        //     $validationInput = $request->validate(
        //         [
        //             'first_name' => 'required|string|max:100',
        //             'last_name' => 'required|string|max:100',
        //             'middle_name' => 'nullable|string|max:100',
        //             'position' => 'required|string|max:100',
        //             'office' => 'required|string|max:100',
        //             'username' => 'required|string|max:100|unique:users,username',
        //             'password' => 'required|string|max:16',
        //         ]
        //     );

        //     $user->update($validationInput);
        //     return response()->json([
        //         'success' => true,
        //         'user' =>  $user
        //     ]);
        // } catch (ValidationException $ve) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Validation error',
        //         'errors' => $ve->errors()
        //     ], 422);
        //     //throw $th;
        // } catch (QueryException $qe) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Database error',
        //         'error' => $qe->getMessage()
        //     ], 500);
        //     //throw $th;
        // } catch (\Throwable $th) {
        //     //throw $th;
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'An unexpected error occurred',
        //         'error' => $th->getMessage()
        //     ], 500);
        // }

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            Log::info('Update User Request:', $request->all()); // Debug incoming data
            $validated = $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'middle_name' => 'nullable|string|max:100',
                'position' => 'required|string|max:100',
                'office' => 'required|string|max:100',
                'username' => 'required|string|max:100|unique:users,username,' . $user->id,
                'password' => 'nullable|string|min:8|max:16|confirmed',
            ]);

            // Only update password if it was provided
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }
           

            $user->update($validated);

            return response()->json([
                'success' => true,
                'user' => $user
            ]);
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
            $user = User::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'user not found'], 404);
            }
            $user->delete();
            return response()->json([
                'success' => true,
                'user' =>  $user
            ], 200);
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



    //-------------------------------------------------------------------LOGIN/LOGOUT--------------------------------------------------------------------------

    // public function login_User(Request $request){
    //     if(!Auth::attempt($request->only('username', 'password')))
    //     {
    //             return response()->json(['login_status'=> false, 'mssage'=> 'Login Failed: Invalid Credentials'],401);
    //     }

    //             $user =  Auth::user();
    //             $token = $user->createToken('CICTMO2025-CHO-INVENTORY-SYSTEM')->plainTextToken;
    //             $cookie = cookie('auth_token', $token, 60*24,null,null,true,true,false,'None');
    //             return response()->json([
    //                     'Login_Status' => true,
    //                     'message'=> 'login successfully',
    //                     'token' => $token

    //             ])->withCookie($cookie);
    // }
}
