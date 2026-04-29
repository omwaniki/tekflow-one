<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Asset;
use App\Models\Campus;
use App\Models\Agent;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 🔥 USE AGENT (same as audit)
        $agent = $user->agent;
        $regionId = optional($agent)->region_id ?? $user->region_id;
        $campusId = optional($agent)->campus_id ?? $user->campus_id;

        // 🔥 REMOVE OLD BLOCK (this caused your 403)
        // if ($user->hasRole(['regional', 'campus']) && !$user->campus) {
        //     abort(403, 'No campus assigned.');
        // }

        // ==============================
        // CAMPUSES (FILTERED CORRECTLY)
        // ==============================
        if ($user->hasRole(['admin', 'global'])) {
            $campuses = Campus::orderBy('name')->get();

        } elseif ($user->hasRole(['regional', 'manager'])) {

            if (!$regionId) {
                abort(403, 'No region assigned.');
            }

            $campuses = Campus::where('region_id', $regionId)
                ->orderBy('name')
                ->get();

        } elseif ($campusId) {

            $campuses = Campus::where('id', $campusId)->get();

        } else {
            abort(403, 'No campus assigned.');
        }

        // ==============================
        // BASE QUERY SCOPE (REUSABLE 🔥)
        // ==============================
        $baseQuery = Asset::with(['campus', 'agent'])

            ->when($user->hasRole(['regional', 'manager']), function ($q) use ($regionId) {
                $q->whereHas('campus', function ($q2) use ($regionId) {
                    $q2->where('region_id', $regionId);
                });
            })

            ->when($user->hasRole(['campus','agent']), function ($q) use ($campusId) {
                $q->where('campus_id', $campusId);
            });

        // ==============================
        // STAFF DEVICES
        // ==============================
        $staffQuery = (clone $baseQuery)->where('type', 'staff');

        if ($request->filled('status')) {
            $staffQuery->where('status', $request->status);
        }

        if ($request->filled('campus_id')) {
            $staffQuery->where('campus_id', $request->campus_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $staffQuery->where(function ($q) use ($search) {
                $q->where('assigned_to_name', 'like', "%{$search}%")
                ->orWhere('assigned_to_email', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%")
                ->orWhere('device_type', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $staffAssets = $staffQuery->latest()->get();

        // ==============================
        // STUDENT DEVICES
        // ==============================
        $studentQuery = (clone $baseQuery)->where('type', 'student');

        if ($request->filled('status')) {
            $studentQuery->where('status', $request->status);
        }

        if ($request->filled('campus_id')) {
            $studentQuery->where('campus_id', $request->campus_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $studentQuery->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                ->orWhere('model', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $studentAssets = $studentQuery->latest()->get();

        // ==============================
        // KPI COUNTS
        // ==============================
        $statusCountsQuery = clone $baseQuery;

        $statusCounts = [
            'active' => (clone $statusCountsQuery)->where('status', 'active')->count(),
            'faulty' => (clone $statusCountsQuery)->where('status', 'faulty')->count(),
            'retired' => (clone $statusCountsQuery)->where('status', 'retired')->count(),
        ];

        return view('assets.index', compact(
            'staffAssets',
            'studentAssets',
            'campuses',
            'statusCounts'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $asset = \App\Models\Asset::with([
            'movements.fromCampus',
            'movements.toCampus',
            'assignments'
        ])->findOrFail($id);

        // 🔥 Movements
        $movementEvents = $asset->movements->map(function ($m) {
            return [
                'type' => 'movement',
                'date' => $m->movement_date,
                'label' => 'Moved '
                    . ($m->fromCampus->name ?? '-')
                    . ' → '
                    . ($m->toCampus->name ?? '-'),
                'meta' => ucfirst(str_replace('_', ' ', $m->movement_type)),
            ];
        })->toBase();

        // 🔥 Assignments
        $assignmentEvents = $asset->assignments->map(function ($a) {
            return [
                'type' => 'assignment',
                'date' => $a->assigned_at ?? $a->created_at,
                'label' => 'Assigned to ' . ($a->assigned_to_name ?? '-'),
                'meta' => ucfirst($a->status ?? 'active'),
            ];
        })->toBase();

        // 🔥 Merge + Sort
        $timeline = $movementEvents
            ->merge($assignmentEvents)
            ->sortByDesc('date')
            ->values();

        return view('assets.show', compact('asset', 'timeline'));
    }

    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user(); // 🔥 ADD THIS
        $campuses = Campus::visibleTo($user)->orderBy('name')->get();
        $agents = Agent::visibleTo($user)->orderBy('name')->get();

        return view('assets.create', compact('campuses', 'agents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'type' => 'required|in:staff,student',
            'assigned_to_name' => 'nullable|string|max:255',
            'assigned_to_email' => 'nullable|email|max:255',
            'role' => 'nullable|string|max:255',
            'device_type' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'required|string|max:255|unique:assets,serial_number',
            'status' => 'required|in:active,faulty,retired',
            'manufacture_date' => 'nullable|date',
            'campus_id' => 'nullable|exists:campuses,id',
            'agent_id' => 'nullable|exists:agents,id',
        ]);

        $data = $request->all();

        // 🔒 FORCE CAMPUS FOR CAMPUS USERS
        $agent = $user->agent;
        $campusId = optional($agent)->campus_id ?? $user->campus_id;

        if ($user->hasRole('campus')) {
            $data['campus_id'] = $campusId;
        }

        // 🔒 REGIONAL / MANAGER VALIDATION
        if ($user->hasRole(['regional', 'manager']) && $request->campus_id) {

            $campus = Campus::find($request->campus_id);

            if (!$campus || $campus->region_id !== $user->region_id) {
                abort(403, 'Unauthorized campus selection.');
            }
        }

        Asset::create($data);

        return redirect()->route('assets.index')
            ->with('success', 'Asset created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();

        $asset = Asset::visibleTo($user)->findOrFail($id);

        $campuses = Campus::visibleTo($user)->orderBy('name')->get();
        $agents = Agent::visibleTo($user)->orderBy('name')->get();

        return view('assets.edit', compact('asset', 'campuses', 'agents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $asset = Asset::visibleTo($user)->findOrFail($id);

        $request->validate([
            'type' => 'required|in:staff,student',
            'assigned_to_name' => 'nullable|string|max:255',
            'assigned_to_email' => 'nullable|email|max:255',
            'role' => 'nullable|string|max:255',
            'device_type' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'required|string|max:255|unique:assets,serial_number,' . $asset->id,
            'status' => 'required|in:active,faulty,retired',
            'manufacture_date' => 'nullable|date',
            'campus_id' => 'nullable|exists:campuses,id',
            'agent_id' => 'nullable|exists:agents,id',
        ]);

        $data = $request->all();

        // 🔒 Campus enforcement
        if ($user->hasRole('campus')) {
            $data['campus_id'] = $user->campus_id;
        }

        // 🔒 Regional enforcement
        if ($user->hasRole(['regional', 'manager']) && $request->campus_id) {

            $campus = Campus::find($request->campus_id);

            if (!$campus || $campus->region_id !== $user->region_id) {
                abort(403, 'Unauthorized campus selection.');
            }
        }

        $asset->update($data); // ✅ FIXED

        return redirect()->route('assets.index')
            ->with('success', 'Asset updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $asset = Asset::visibleTo($user)->findOrFail($id);
        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    /**
     * Inline status update (optional)
     */
    public function updateStatus(Request $request, Asset $asset)
    {
        $user = Auth::user();

        $asset = Asset::visibleTo($user)->findOrFail($asset->id);

        $request->validate([
            'status' => 'required|in:active,faulty,retired'
        ]);

        $asset->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Download template
     */
    public function downloadTemplate()
    {
        return redirect('/assets/template/staff');
    }

    /**
     * Download template staff
     */
    public function downloadStaffTemplate()
    {
        $headers = [
            'assigned_to_name',
            'assigned_to_email',
            'role',
            'device_type',
            'serial_number',
            'status',
            'manufacture_date',
            'campus_name'
        ];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, $headers);

            // Example row
            fputcsv($file, [
                'John Doe',
                'john@email.com',
                'Teacher',
                'Laptop',
                'SN001',
                'active',
                '2024-01-01',
                'Tatu City'
            ]);

            fclose($file);
        };

        return response()->streamDownload($callback, 'staff_assets_template.csv');
    }

    /**
     * Download template student
     */
    public function downloadStudentTemplate()
    {
        $headers = [
            'device_type',
            'brand',
            'model',
            'serial_number',
            'status',
            'manufacture_date',
            'campus_name'
        ];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, $headers);

            // Example row
            fputcsv($file, [
                'Chromebook',
                'Lenovo',
                '100e',
                'SN002',
                'active',
                '2024-01-01',
                'Boksburg'
            ]);

            fclose($file);
        };

        return response()->streamDownload($callback, 'student_assets_template.csv');
    }

    /**
     * Immport data
     */

    public function import(Request $request)
    {
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'mimes:csv,txt'
            ]);
        } elseif (!$request->input('file')) {
            abort(400, 'No file provided');
        }

        $type = $request->input('type');
        if (!$type) {
            dd('TYPE IS MISSING'); // 🔥 DEBUG
        }
        // 🔥 Handle both upload & preview confirm properly
        if ($request->hasFile('file')) {

            // Direct upload (fallback)
            $filePath = $request->file('file')->store('temp');

        } else {

            // From preview confirm
            $filePath = $request->input('file');
        }

        // ✅ Always resolve path using Laravel
        $fullPath = Storage::path($filePath);
        $file = fopen($fullPath, 'r');

        $header = fgetcsv($file);

        $success = 0;
        $failed = 0;
        $errors = [];

        $rowNumber = 1;

        $seenSerials = []; // ✅ Track duplicates inside CSV

        while ($row = fgetcsv($file)) {

            $rowNumber++;

            $data = array_combine($header, $row);

            try {

                // =========================
                // VALIDATION
                // =========================
                if (empty($data['serial_number'])) {
                    throw new \Exception("Missing serial_number");
                }

                if (!in_array($type, ['staff', 'student'])) {
                    throw new \Exception("Invalid upload type selected");
                }

                // ✅ Duplicate inside CSV
                if (in_array($data['serial_number'], $seenSerials)) {
                    throw new \Exception("Duplicate serial_number in file");
                }

                $seenSerials[] = $data['serial_number'];

                // ✅ Duplicate in database
                if (\App\Models\Asset::where('serial_number', $data['serial_number'])->exists()) {
                    throw new \Exception("Duplicate serial_number in system");
                }

                // =========================
                // 🔥 TYPE-BASED VALIDATION
                // =========================

                if ($type === 'staff') {

                    if (empty($data['assigned_to_name'])) {
                        throw new \Exception("Missing staff name");
                    }

                    if (empty($data['assigned_to_email'])) {
                        throw new \Exception("Missing staff email");
                    }

                    if (empty($data['role'])) {
                        throw new \Exception("Missing role");
                    }

                    if (empty($data['device_type'])) {
                        throw new \Exception("Missing device_type");
                    }
                }

                if ($type === 'student') {

                    if (empty($data['device_type'])) {
                        throw new \Exception("Missing device_type");
                    }

                    if (empty($data['brand'])) {
                        throw new \Exception("Missing brand");
                    }

                    if (empty($data['model'])) {
                        throw new \Exception("Missing model");
                    }
                }

                if (!empty($data['status']) && !in_array($data['status'], ['active', 'faulty', 'retired'])) {
                    throw new \Exception("Invalid status");
                }

                // ✅ Validate campus
                // =========================
                // 🔥 SMART CAMPUS MAPPING
                // =========================

                $campusId = null;

                // Prefer campus_name
                if (!empty($data['campus_name'])) {

                    $campusName = trim($data['campus_name']);

                    $campus = \App\Models\Campus::whereRaw(
                        'LOWER(name) LIKE ?',
                        ['%' . strtolower($campusName) . '%']
                    )->first();

                    if (!$campus) {
                        throw new \Exception("Invalid campus_name: {$campusName}");
                    }

                    $campusId = $campus->id;

                }
                // Fallback to campus_id (optional)
                elseif (!empty($data['campus_id'])) {

                    $campus = \App\Models\Campus::find($data['campus_id']);

                    if (!$campus) {
                        throw new \Exception("Invalid campus_id");
                    }

                    $campusId = $campus->id;
                }

                // =========================
                // 🔒 ACCESS CONTROL (ADD THIS HERE)
                // =========================

                $user = Auth::user();

                // 🔥 Campus users → force their campus
                if ($user->hasRole('campus')) {
                    $campusId = $user->campus_id;
                }

                // 🔥 Regional / Manager → validate campus belongs to their region
                if ($user->hasRole(['regional', 'manager'])) {

                    if ($campusId) {

                        $campus = \App\Models\Campus::find($campusId);

                        if (!$campus || $campus->region_id !== $user->region_id) {
                            throw new \Exception("Unauthorized campus for your region");
                        }
                    }
                }

                // =========================
                // CREATE
                // =========================
                \App\Models\Asset::create([
                    'type' => $type,

                    'assigned_to_name' => $data['assigned_to_name'] ?? null,
                    'assigned_to_email' => $data['assigned_to_email'] ?? null,
                    'role' => $data['role'] ?? null,
                    'device_type' => $data['device_type'] ?? null,

                    // ✅ FIX HERE
                    'brand' => $data['brand'] ?? null,
                    'model' => $data['model'] ?? null,

                    'serial_number' => $data['serial_number'],
                    'status' => $data['status'] ?? 'active',

                    'manufacture_date' => !empty($data['manufacture_date'])
                        ? date('Y-m-d', strtotime($data['manufacture_date']))
                        : null,

                    'campus_id' => $campusId,
                ]);

                $success++;

            } catch (\Exception $e) {
                dd($e->getMessage(), $data); // 🔥 TEMP DEBUG
            }
        }

        fclose($file);

        return redirect()->back()->with([
            'import_summary' => [
                'success' => $success,
                'failed' => $failed,
                'errors' => $errors
            ]
        ]);
    }
    /**
     * Immport data peview
     */

    public function preview(Request $request)
    {
        $file = $request->file('file');

        // Ensure temp folder exists
        if (!Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }

        $path = $file->store('temp');

        $fullPath = Storage::path($path);
        $handle = fopen($fullPath, 'r');

        $headers = fgetcsv($handle);
        $rows = [];

        while ($row = fgetcsv($handle)) {
            $rows[] = $row;
        }

        fclose($handle);

        return back()->with('preview_data', [
            'headers' => $headers,
            'rows' => array_slice($rows, 0, 10),
            'file' => $path,
            'type' => $request->input('type') // ✅ ADD THIS LINE
        ]);
    }

    /**
     * Faiiled upload download
     */

    public function downloadFailed()
    {
        $errors = session('import_summary.errors', []);

        $callback = function () use ($errors) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['error']);

            foreach ($errors as $error) {
                fputcsv($file, [$error]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 'failed_rows.csv');
    }

    /*Asset Timeline*/

    public function timeline($id)
    {
        $asset = \App\Models\Asset::with([
            'movements.fromCampus',
            'movements.toCampus',
            'assignments'
        ])->findOrFail($id);

        // 🔥 Movements
        $movementEvents = $asset->movements->map(function ($m) {
            return [
                'type' => 'movement',
                'date' => $m->movement_date,
                'label' => 'Moved '
                    . ($m->fromCampus->name ?? '-')
                    . ' → '
                    . ($m->toCampus->name ?? '-'),
                'meta' => ucfirst(str_replace('_', ' ', $m->movement_type)),
                'color' => 'purple'
            ];
        })->toBase(); // 🔥 IMPORTANT

        // 🔥 Assignments
        $assignmentEvents = $asset->assignments->map(function ($a) {
            return [
                'type' => 'assignment',
                'date' => $a->assigned_at ?? $a->created_at,
                'label' => 'Assigned to ' . ($a->assigned_to_name ?? '-'),
                'meta' => ucfirst($a->status ?? 'active'),
                'color' => 'blue'
            ];
        })->toBase(); // 🔥 IMPORTANT

        // 🔥 Merge + Sort
        $timeline = $movementEvents
            ->merge($assignmentEvents)
            ->sortByDesc('date')
            ->values();

        return view('assets.timeline', compact('asset', 'timeline'));
    }

}