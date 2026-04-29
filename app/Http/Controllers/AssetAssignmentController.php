<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetAssignmentController extends Controller
{
    /**
     * Display assignments list
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = AssetAssignment::with(['asset', 'campus', 'assignedBy'])
            ->visibleTo($user)
            ->latest();

        // 🔍 Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('assigned_to_name', 'like', '%' . $request->search . '%')
                  ->orWhere('assigned_to_email', 'like', '%' . $request->search . '%')
                  ->orWhereHas('asset', function ($a) use ($request) {
                      $a->where('serial_number', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $assignments = $query->paginate(10);

        return view('asset_assignments.index', compact('assignments'));
    }

    /**
     * Store new assignment
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'assigned_to_name' => 'required|string|max:255',
            'assigned_to_email' => 'nullable|email',
            'assigned_to_type' => 'required|string',
            'campus_id' => 'nullable|exists:campuses,id',
            'notes' => 'nullable|string'
        ]);

        $user = Auth::user();

        $asset = Asset::findOrFail($request->asset_id);

        // 🔥 Close any existing active assignment
        AssetAssignment::where('asset_id', $asset->id)
            ->where('status', 'active')
            ->update([
                'status' => 'returned',
                'returned_at' => now()
            ]);

        // ✅ Create new assignment
        AssetAssignment::create([
            'asset_id' => $asset->id,
            'assigned_to_name' => $request->assigned_to_name,
            'assigned_to_email' => $request->assigned_to_email,
            'assigned_to_type' => $request->assigned_to_type,
            'campus_id' => $request->campus_id ?? $asset->campus_id,
            'assigned_by' => $user->id,
            'assigned_at' => now(),
            'status' => 'active',
            'notes' => $request->notes,
        ]);

        // 🔁 (Optional sync for now — keep UI consistent)
        $asset->update([
            'assigned_to_name' => $request->assigned_to_name,
            'assigned_to_email' => $request->assigned_to_email,
        ]);

        return redirect()->back()->with('success', 'Asset assigned successfully.');
    }

    /**
     * Return asset
     */
    public function return($id)
    {
        $assignment = AssetAssignment::findOrFail($id);

        $assignment->update([
            'status' => 'returned',
            'returned_at' => now()
        ]);

        return redirect()->back()->with('success', 'Asset returned successfully.');
    }


    public function create()
    {
        $user = auth()->user();

        // 🔴 Prevent crash if user is null
        if (!$user) {
            return redirect()->route('login');
        }

        $assets = \App\Models\Asset::query()
            ->when(!$user->hasRole(['admin', 'global']), function ($q) use ($user) {

                if ($user->hasRole(['regional', 'manager'])) {
                    $q->whereHas('campus', function ($c) use ($user) {
                        $c->where('region_id', $user->region_id);
                    });
                } else {
                    $q->where('campus_id', $user->campus_id);
                }

            })
            ->get();

        $campuses = \App\Models\Campus::query()
            ->when(!$user->hasRole(['admin', 'global']), function ($q) use ($user) {

                if ($user->hasRole(['regional', 'manager'])) {
                    $q->where('region_id', $user->region_id);
                } else {
                    $q->where('id', $user->campus_id);
                }

            })
            ->get();

        return view('asset_assignments.create', compact('assets', 'campuses'));
    }
}