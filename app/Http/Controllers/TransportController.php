<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    /**
     * Store a new transport pickup request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'pickup-location' => 'required|string|max:1000',
        ]);

        $transport = Transport::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'pickup_location' => $validated['pickup-location'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pickup request submitted successfully. We will contact you soon!',
            'data' => $transport,
        ], 201);
    }



    /**
     * Update transport request status
     */
    public function updateStatus(Request $request, Transport $transport)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $transport->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'processed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transport request status updated successfully',
            'data' => $transport,
        ]);
    }

    /**
     * Delete transport request
     */
    public function destroy(Transport $transport)
    {
        $transport->delete();
        return response()->json([
            'success' => true,
            'message' => 'Transport request deleted successfully',
        ]);
    }
}
