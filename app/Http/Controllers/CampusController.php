<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\Region;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    /**
     * Display a listing of campuses
     */
    public function index()
    {
        $user = auth()->user();

        $campuses = Campus::visibleTo($user)
            ->with('region')
            ->latest()
            ->paginate(10);

        return view('campuses.index', compact('campuses'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $user = auth()->user();

        // 🔒 Restrict campus users
        if ($user->hasRole('campus')) {
            abort(403, 'You do not have permission to create campuses.');
        }

        // 🔒 Regional users → only their region
        if ($user->hasRole(['regional', 'manager'])) {
            $regions = Region::where('id', $user->region_id)->get();
        } else {
            $regions = Region::orderBy('name')->get();
        }

        return view('campuses.create', compact('regions'));
    }

    /**
     * Store new campus
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id'
        ]);

        // 🔒 Regional restriction
        if ($user->hasRole(['regional', 'manager'])) {
            if ($validated['region_id'] != $user->region_id) {
                abort(403, 'Unauthorized region.');
            }
        }

        Campus::create($validated);

        return redirect()
            ->route('campuses.index')
            ->with('success', 'Campus created successfully.');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $user = auth()->user();

        $campus = Campus::visibleTo($user)->findOrFail($id);

        // 🔒 Regional users → only their region
        if ($user->hasRole(['regional', 'manager'])) {
            $regions = Region::where('id', $user->region_id)->get();
        } else {
            $regions = Region::orderBy('name')->get();
        }

        return view('campuses.edit', compact('campus', 'regions'));
    }

    /**
     * Update campus
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        $campus = Campus::visibleTo($user)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id'
        ]);

        // 🔒 Regional restriction
        if ($user->hasRole(['regional', 'manager'])) {
            if ($validated['region_id'] != $user->region_id) {
                abort(403, 'Unauthorized region.');
            }
        }

        $campus->update($validated);

        return redirect()
            ->route('campuses.index')
            ->with('success', 'Campus updated successfully.');
    }

    /**
     * Delete campus
     */
    public function destroy($id)
    {
        $user = auth()->user();

        $campus = Campus::visibleTo($user)->findOrFail($id);

        // 🔒 Prevent deleting if assets exist
        if ($campus->assets()->count() > 0) {
            return redirect()
                ->route('campuses.index')
                ->with('error', 'Cannot delete campus because assets are assigned to it.');
        }

        $campus->delete();

        return redirect()
            ->route('campuses.index')
            ->with('success', 'Campus deleted successfully.');
    }
}