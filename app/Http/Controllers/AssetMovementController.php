<?php

namespace App\Http\Controllers;

use App\Models\AssetMovement;
use App\Models\Asset;
use App\Models\Campus;
use Illuminate\Http\Request;

class AssetMovementController extends Controller
{
    /**
     * Display movements list
     */
    public function index(Request $request)
    {
        $query = AssetMovement::with([
            'asset',
            'fromCampus',
            'toCampus',
            'performedBy'
        ]);

        // 🔍 OPTIONAL FILTERS (safe — won’t break anything)
        if ($request->movement_type) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->campus_id) {
            $query->where('to_campus_id', $request->campus_id);
        }

        if ($request->date) {
            $query->whereDate('movement_date', $request->date);
        }

        $movements = $query->latest()->paginate(10);

        return view('movements.index', compact('movements'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $assets = Asset::with('campus')->get();
        $campuses = Campus::all();

        return view('movements.create', compact('assets', 'campuses'));
    }

    /**
     * Store movement
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'to_campus_id' => 'required|exists:campuses,id',
            'movement_type' => 'required|string',
            'reason' => 'nullable|string',
            'movement_date' => 'required|date',
        ]);

        // 🔍 Get asset
        $asset = Asset::findOrFail($data['asset_id']);

        // 🔥 Create movement record
        AssetMovement::create([
            'asset_id' => $asset->id,
            'from_campus_id' => $asset->campus_id,
            'to_campus_id' => $data['to_campus_id'],
            'movement_type' => $data['movement_type'],
            'reason' => $data['reason'],
            'movement_date' => $data['movement_date'],
            'performed_by' => auth()->id(),
        ]);

        // 🔥 Update asset location
        $asset->update([
            'campus_id' => $data['to_campus_id']
        ]);

        return redirect()->route('movements.index')
            ->with('success', 'Asset moved successfully');
    }
}