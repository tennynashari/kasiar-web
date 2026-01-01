<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OutletController extends Controller
{
    public function index(Request $request)
    {
        // Only owner can access all outlets
        if ($request->user()->role !== 'owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $outlets = Outlet::withCount(['users', 'tables'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($outlets);
    }

    public function store(Request $request)
    {
        // Only owner can create outlets
        if ($request->user()->role !== 'owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:outlets,code|max:50',
            'business_type' => 'required|in:retail,minimarket,fnb',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'enable_qr_order' => 'boolean',
        ]);

        $outlet = Outlet::create($validated);

        return response()->json($outlet, 201);
    }

    public function show(Request $request, Outlet $outlet)
    {
        // Only owner can view outlet details
        if ($request->user()->role !== 'owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $outlet->load(['users', 'tables']);
        $outlet->loadCount(['users', 'tables']);

        return response()->json($outlet);
    }

    public function update(Request $request, Outlet $outlet)
    {
        // Only owner can update outlets
        if ($request->user()->role !== 'owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:outlets,code,' . $outlet->id,
            'business_type' => 'required|in:retail,minimarket,fnb',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'enable_qr_order' => 'boolean',
        ]);

        $outlet->update($validated);

        return response()->json($outlet);
    }

    public function destroy(Request $request, Outlet $outlet)
    {
        // Only owner can delete outlets
        if ($request->user()->role !== 'owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if outlet has users
        if ($outlet->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete outlet with active users. Please reassign users first.'
            ], 422);
        }

        $outlet->delete();

        return response()->json(['message' => 'Outlet deleted successfully']);
    }

    public function generateQrCodes(Request $request, Outlet $outlet)
    {
        // Only owner can generate QR codes
        if ($request->user()->role !== 'owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'table_count' => 'required|integer|min:1|max:100',
        ]);

        $baseUrl = config('app.frontend_url', 'http://localhost:5173');
        $qrCodes = [];

        for ($i = 1; $i <= $validated['table_count']; $i++) {
            $qrCodes[] = [
                'table_number' => $i,
                'url' => "{$baseUrl}/order/{$outlet->id}/{$i}",
                'qr_data' => "{$baseUrl}/order/{$outlet->id}/{$i}"
            ];
        }

        return response()->json([
            'outlet' => $outlet,
            'qr_codes' => $qrCodes
        ]);
    }
}
