<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditTrail;

class AuditController extends Controller
{
    //
    // Get all audit logs
    public function index()
    {
        $logs = AuditTrail::latest()->get();
        return response()->json($logs);
    }

    // Store a new audit log
    public function store(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string',
            'table_name' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'changes' => 'required|string',
        ]);

        $log = AuditTrail::create($validated);

        return response()->json([
            'message' => 'Audit log created successfully.',
            'data' => $log
        ], 201);
    }

    // Show a specific audit log
    public function show($id)
    {
        $log = AuditTrail::where('id', $id)->get();
        return response()->json($log);
    }

    public function showAllLogs($user_id)
    {
        $log = AuditTrail::where('user_id', $user_id)->get();
        return response()->json($log);
    }

    // Update a specific audit log (rarely used in audit logs)
    public function update(Request $request, $id)
    {
        $log = AuditTrail::findOrFail($id);

        $validated = $request->validate([
            'action' => 'sometimes|string|max:255',
            'table_name' => 'sometimes|string|max:255',
            'user_id' => 'sometimes|integer',
            'changes' => 'nullable|string',
        ]);

        $log->update($validated);

        return response()->json([
            'message' => 'Audit log updated successfully.',
            'data' => $log
        ]);
    }

    // Delete a specific audit log (not recommended)
    public function destroy($id)
    {
        $log = AuditTrail::findOrFail($id);
        $log->delete();

        return response()->json([
            'message' => 'Audit log deleted successfully.'
        ]);
    }
}
