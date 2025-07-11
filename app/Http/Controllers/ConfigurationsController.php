<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Illuminate\Http\Request;
use App\Models\Configurations;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function PHPUnit\Framework\isEmpty;

class ConfigurationsController extends Controller
{
    //
    public function store(Request $request)
    {

        try {
            //code...
            $validationInput = $request->validate(
                [
                    'low_count' => 'nullable|integer',
                    'days_toExpire' => 'nullable|integer',
                    'normal_color' => 'nullable|string',
                    'low_color' => 'nullable|string',
                    'empty_color' => 'nullable|string',
                ]
            );
            // $config = Configurations::create($validationInput);
             $config = Configurations::updateOrCreate(['id' => 1], $validationInput);

            return response()->json([
                'success' => true,
                'configuration' =>  $config
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
        } catch (Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    public function updateConfig(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'low_count' => 'nullable|integer',
                'days_toExpire' => 'nullable|integer',
                'normal_color' => 'nullable|string',
                'low_color' => 'nullable|string',
                'empty_color' => 'nullable|string',
            ]);

            $config = Configurations::findOrFail($id);
            $config->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Configuration updated successfully.',
                'configuration' => $config,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Configuration not found.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $config = Configurations::findOrFail($id);

            if (empty($config)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Configuration Set',
                ]);
            }

            return response()->json([
                'success' => true,
                'configuration' => $config,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Configuration not found.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $config = Configurations::findOrFail($id);
            $config->delete();

            return response()->json([
                'success' => true,
                'message' => 'Configuration deleted successfully.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Configuration not found.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
