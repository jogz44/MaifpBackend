<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    //

    public function Monthly_Dispense()
    {
        // Fetch all montly dispense reports
        try {
            $reports_monthly_dispense = DB::table('vw_monthly_dispense_report')->get();
            return response()->json(['dispense' => $reports_monthly_dispense], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Database query error', 'message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }
}
