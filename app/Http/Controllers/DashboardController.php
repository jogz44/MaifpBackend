<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    //
    public function dashboard_registered_customers()
    {
        try {
            // Validate the request parameters
            $validatedData = request()->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            if (!$validatedData) {
                return response()->json(['error' => 'Invalid date range'], 400);
            }
            // Fetch the registered customers within the specified date range



            return $this->getRegisteredCustomers($validatedData['start_date'], $validatedData['end_date']);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation error', 'messages' => $e->errors()], 422);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Unexpected error', 'message' => $e->getMessage()], 500);
        }
    }

    private function getRegisteredCustomers($start_date, $end_date)
    {
        try {
            $customers = DB::table('TBL_CUSTOMERS')
                ->select(
                    DB::raw('COUNT(*) as registered_Customers')
                )
                ->whereBetween('created_at', [$start_date, $end_date])
                ->first();
            if (!$customers) {
                return response()->json(['message' => 'No customers found for the specified date range'], 404);
            }
            // Format the response
            return response()->json($customers);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database query error', 'message' => $e->getMessage()], 500);
        } catch (Throwable $e) {
            return response()->json(['error' => 'Unexpected error', 'message' => $e->getMessage()], 500);
        }
    }


    public function dashboard_served_customers()
    {
        try {
            // Validate the request parameters
            $validatedData = request()->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            if (!$validatedData) {
                return response()->json(['error' => 'Invalid date range'], 400);
            }

            // Fetch the served customers within the specified date range
            // return $this->getServedCustomers($validatedData['start_date'], $validatedData['end_date']);

            $servedCustomers = DB::table('vw_recipient_dispense')
                ->select(DB::raw('COUNT(DISTINCT transaction_id) as served_Customers'))
                ->whereBetween('transaction_date', [$validatedData['start_date'], $validatedData['end_date']])
                ->first();

            if (!$servedCustomers) {
                return response()->json(['message' => 'No served customers found for the specified date range'], 404);
            }

            return response()->json($servedCustomers);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation error', 'messages' => $e->errors()], 422);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Unexpected error', 'message' => $e->getMessage()], 500);
        }
    }

    public function dashboard_customers_genders()
    {
        try {
            // Validate the request parameters
            $validatedData = request()->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            if (!$validatedData) {
                return response()->json(['error' => 'Invalid date range'], 400);
            }

            // Fetch the served customers within the specified date range
            // return $this->getServedCustomers($validatedData['start_date'], $validatedData['end_date']);

            $servedCustomers = DB::table('vw_recipient_dispense')

                ->select('gender', DB::raw('COUNT(DISTINCT transaction_id) as count'))
                ->whereBetween('transaction_date', [$validatedData['start_date'], $validatedData['end_date']])
                ->groupBy('gender')
                ->get();

            if (!$servedCustomers) {
                return response()->json(['message' => 'No served customers found for the specified date range'], 404);
            }

            $totalServed = $servedCustomers->sum('count');

            return response()->json([
                'total' => $totalServed,
                'by_gender' => $servedCustomers
            ]);

            // return response()->json($servedCustomers);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation error', 'messages' => $e->errors()], 422);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Unexpected error', 'message' => $e->getMessage()], 500);
        }
    }

    public function dashboard_customers_ages(){

             try {
        // Validate the request parameters
        $validatedData = request()->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $CustomerAges = DB::table('vw_recipient_dispense')
            ->whereBetween('transaction_date', [$validatedData['start_date'], $validatedData['end_date']])
            ->selectRaw('
                COUNT(DISTINCT transaction_id) as total,
                COUNT(CASE WHEN age BETWEEN 0 AND 17 THEN 1 END) AS age_0_17,
                COUNT(CASE WHEN age BETWEEN 18 AND 59 THEN 1 END) AS age_18_59,
                COUNT(CASE WHEN age >= 60 THEN 1 END) AS age_60_above
            ')
            ->first();

        if (!$CustomerAges) {
            return response()->json(['message' => 'No served customers found for the specified date range'], 404);
        }

        return response()->json([
            'total' => $CustomerAges->total,
            'children' => $CustomerAges->age_0_17,
            'adults' => $CustomerAges->age_18_59,
            'seniors' => $CustomerAges->age_60_above,
        ]);
    } catch (ValidationException $e) {
        return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
    } catch (Exception $e) {
        return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
    }

    }

}
