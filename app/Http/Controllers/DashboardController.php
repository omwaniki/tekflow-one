<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Campus;
use App\Models\Region;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ==============================
        // SUMMARY STATS
        // ==============================
        $totalAssets = Asset::count();
        $totalCampuses = Campus::count();
        $totalRegions = Region::count();

        // ==============================
        // ASSETS BY CAMPUS
        // ==============================
        $assetsByCampus = Campus::withCount('assets')->get();

        // ==============================
        // ✅ ASSET STATUS AGGREGATION
        // ==============================
        $statusData = Asset::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Ensure all statuses always exist
        $statuses = [
            'active' => $statusData['active'] ?? 0,
            'faulty' => $statusData['faulty'] ?? 0,
            'retired' => $statusData['retired'] ?? 0,
        ];

        return view('dashboard', compact(
            'totalAssets',
            'totalCampuses',
            'totalRegions',
            'assetsByCampus',
            'statuses' // 👈 NEW
        ));
    }
}