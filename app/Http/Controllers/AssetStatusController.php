<?php

namespace App\Http\Controllers;

use App\Models\AssetStatus;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AssetStatusController extends Controller
{
    public function index()
    {
        // ✅ Load statuses WITH usage count (efficient, no N+1 queries)
        $statuses = AssetStatus::withCount([
            'assets as assets_count'
        ])->latest()->get();

        return view('settings.asset-statuses.index', compact('statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:asset_statuses,name',
            'color' => 'required|string'
        ]);

        AssetStatus::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'color' => $request->color,
            'is_active' => true
        ]);

        return back()->with('success', 'Status added successfully');
    }

    public function update(Request $request, $id)
    {
        $status = AssetStatus::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:asset_statuses,name,' . $id,
            'color' => 'required|string'
        ]);

        // 🚨 Check if status is used
        $isUsed = Asset::where('status', $status->name)->exists();

        // Prevent renaming if already used
        if ($isUsed && $status->name !== $request->name) {
            return back()->withErrors('This status is already assigned to assets and cannot be renamed.');
        }

        $status->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'color' => $request->color,
            'is_active' => $request->has('is_active')
        ]);

        return back()->with('success', 'Status updated successfully');
    }

    public function destroy($id)
    {
        $status = AssetStatus::findOrFail($id);

        // 🚨 Check if status is used
        $isUsed = Asset::where('status', $status->name)->exists();

        if ($isUsed) {
            return back()->withErrors('This status is already assigned to assets and cannot be deleted.');
        }

        $status->delete();

        return back()->with('success', 'Status deleted successfully');
    }
}