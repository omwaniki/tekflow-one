<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{

    /**
     * Display a listing of regions
     */
    public function index()
    {
        $regions = Region::latest()->paginate(10);

        return view('regions.index', compact('regions'));
    }


    /**
     * Show the form for creating a new region
     */
    public function create()
    {
        return view('regions.create');
    }


    /**
     * Store a newly created region
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:regions,name'
        ]);

        Region::create($validated);

        return redirect()
            ->route('regions.index')
            ->with('success', 'Region created successfully.');
    }


    /**
     * Display a specific region
     */
    public function show(Region $region)
    {
        return view('regions.show', compact('region'));
    }


    /**
     * Show the form for editing the region
     */
    public function edit(Region $region)
    {
        return view('regions.edit', compact('region'));
    }


    /**
     * Update the specified region
     */
    public function update(Request $request, Region $region)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:regions,name,' . $region->id
        ]);

        $region->update($validated);

        return redirect()
            ->route('regions.index')
            ->with('success', 'Region updated successfully.');
    }


    /**
     * Remove the specified region
     */
    public function destroy(Region $region)
    {
        $region->delete();

        return redirect()
            ->route('regions.index')
            ->with('success', 'Region deleted successfully.');
    }
}