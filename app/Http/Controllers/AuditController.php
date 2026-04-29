<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Asset;
use App\Models\AuditRecord;
use App\Models\Campus;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::query();

        // 🔍 SEARCH (NEW)
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // 🔁 SAME AS BEFORE
        $audits = $query->latest()->paginate(10);

        // 🔥 AJAX (ONLY for live search)
        if ($request->ajax()) {

            $rows = '';

            foreach ($audits as $audit) {

                $statusClass = $audit->status === 'active'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-gray-100 text-gray-700';

                $rows .= '
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 font-medium text-gray-800">'.$audit->name.'</td>

        <td class="px-6 py-4 capitalize">
            <span class="px-2 py-1 text-xs rounded-full '.$statusClass.'">
                '.$audit->status.'
            </span>
        </td>

        <td class="px-6 py-4 text-gray-600">
            '.$audit->created_at->format('d M Y').'
        </td>

        <td class="px-6 py-4 text-right">
            <div class="flex justify-end gap-3">

                <a href="'.route('audits.dashboard', $audit->id).'"
                class="text-blue-600 hover:underline text-sm">
                    Dashboard
                </a>

                <a href="'.route('audits.edit', $audit->id).'"
                class="text-gray-600 hover:underline text-sm">
                    Edit
                </a>

                <form method="POST" action="'.route('audits.destroy', $audit->id).'"
                    onsubmit="return confirm(\'Delete this audit?\')">
                    '.csrf_field().method_field('DELETE').'

                    <button class="text-red-600 hover:underline text-sm">
                        Delete
                    </button>
                </form>

            </div>
        </td>
    </tr>';
            }

            if ($rows === '') {
                $rows = '
    <tr>
        <td colspan="4" class="text-center py-10 text-gray-400">
            No audits found
        </td>
    </tr>';
            }

            return response()->json([
                'table' => $rows,
                'pagination' => $audits->links()->toHtml()
            ]);
        }

        return view('audits.index', compact('audits'));
    }

    public function create()
    {
        return view('audits.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        // 🔥 GET REGION FROM AGENT (NOT USER)
        $regionId = optional($user->agent)->region_id ?? $user->region_id;

        $audit = Audit::create([
            'name' => $request->name,
            'created_by' => $user->id,
            'status' => 'active',
        ]);

        $assets = Asset::with('campus')
            ->when($user->hasRole('regional'), function ($q) use ($regionId) {
                $q->whereHas('campus', function ($q2) use ($regionId) {
                    $q2->where('region_id', $regionId);
                });
            })
            ->when($user->hasRole(['campus','agent']), function ($q) use ($user) {
                $q->where('campus_id', $user->campus_id);
            })
            ->get();

        foreach ($assets as $asset) {
            AuditRecord::create([
                'audit_id' => $audit->id,
                'asset_id' => $asset->id,
                'campus_id' => $asset->campus_id,
                'agent_id' => $asset->agent_id ?? $user->id,
                'expected_status' => $asset->status,
            ]);
        }

        return redirect()
            ->route('audits.index')
            ->with('success', 'Audit created successfully.');
    }

    public function verify(Request $request, Audit $audit)
    {
        $user = auth()->user();

        // 🔥 FIX REGION SOURCE
        $regionId = optional($user->agent)->region_id ?? $user->region_id;

        $query = AuditRecord::with('asset', 'campus')
            ->where('audit_id', $audit->id);

        if ($user->hasRole(['admin', 'global'])) {
            $userCampuses = Campus::orderBy('name')->get();

            if ($request->filled('campus_id')) {
                $query->where('campus_id', $request->campus_id);
            }

        } elseif ($user->hasRole(['regional', 'manager'])) {

            if (!$regionId) {
                abort(403, 'User must have a region assigned.');
            }

            $userCampuses = Campus::where('region_id', $regionId)
                ->orderBy('name')
                ->get();

            $query->whereHas('campus', function ($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });

            if ($request->filled('campus_id')) {
                $query->where('campus_id', $request->campus_id);
            }

        } elseif ($user->campus_id) {

            $userCampuses = Campus::where('id', $user->campus_id)->get();
            $query->where('campus_id', $user->campus_id);

        } else {
            abort(403, 'Unauthorized');
        }

        $records = $query->get();

        $total = $records->count();
        $completed = $records->whereNotNull('found')->count();

        $selectedCampus = null;

        if ($request->filled('campus_id')) {
            $selectedCampus = Campus::find($request->campus_id);
        } elseif ($user->campus_id) {
            $selectedCampus = Campus::find($user->campus_id);
        }

        return view('audits.verify', compact(
            'audit',
            'records',
            'total',
            'completed',
            'userCampuses',
            'selectedCampus'
        ));
    }

    public function dashboard(Request $request, Audit $audit)
    {
        $user = auth()->user();

        // 🔥 FIX REGION SOURCE
        $regionId = optional($user->agent)->region_id ?? $user->region_id;

        $statusFilter = $request->status;
        $campusId = $request->campus_id;

        $query = AuditRecord::with('asset', 'campus')
            ->where('audit_id', $audit->id);

        if ($user->hasRole(['admin', 'global'])) {

            $campuses = Campus::orderBy('name')->get();

        } elseif ($user->hasRole(['regional', 'manager'])) {

            if (!$regionId) {
                abort(403, 'User must have a region assigned.');
            }

            $campuses = Campus::where('region_id', $regionId)
                ->orderBy('name')
                ->get();

            $query->whereHas('campus', function ($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });

        } elseif ($user->campus_id) {

            $campuses = Campus::where('id', $user->campus_id)->get();
            $query->where('campus_id', $user->campus_id);

        } else {
            abort(403, 'User must have campus or region assigned.');
        }

        if ($campusId) {
            $query->where('campus_id', $campusId);
        }

        $records = $query->get();

        $total = $records->count();
        $verified = $records->whereNotNull('found')->count();
        $missing = $records->where('found', 0)->count();

        $discrepancies = $records->map(function ($r) {
            if ($r->found === 1) {
                $suggested = 'active';
            } elseif ($r->found === 0) {
                $suggested = 'missing';
            } else {
                return null;
            }

            if (strtolower(trim($suggested)) !== strtolower(trim($r->expected_status))) {
                return [
                    'device' => $r->asset->brand ?? $r->asset->device_type ?? 'Unknown',
                    'serial' => $r->asset->serial_number,
                    'current' => $r->expected_status,
                    'suggested' => $suggested,
                    'record' => $r,
                ];
            }

            return null;
        })->filter();

        $campusProgress = [];

        foreach ($campuses as $campus) {
            $campusRecords = $records->where('campus_id', $campus->id);

            $totalCampus = $campusRecords->count();
            $verifiedCampus = $campusRecords->where('found', 1)->count();

            $percent = $totalCampus > 0
                ? round(($verifiedCampus / $totalCampus) * 100)
                : 0;

            if ($totalCampus === 0 || $verifiedCampus === 0) {
                $status = 'not_started';
            } elseif ($verifiedCampus === $totalCampus) {
                $status = 'completed';
            } else {
                $status = 'in_progress';
            }

            if ($statusFilter && $status !== $statusFilter) {
                continue;
            }

            $campusProgress[] = [
                'id' => $campus->id,
                'name' => $campus->name,
                'total' => $totalCampus,
                'verified' => $verifiedCampus,
                'percent' => $percent,
                'status' => $status,
            ];
        }

        $campusStats = [];

        foreach ($campuses as $campus) {
            $campusRecords = $records->where('campus_id', $campus->id);

            $totalCampus = $campusRecords->count();
            $verifiedCampus = $campusRecords->where('found', 1)->count();

            $percent = $totalCampus > 0
                ? round(($verifiedCampus / $totalCampus) * 100)
                : 0;

            if ($totalCampus === 0 || $verifiedCampus === 0) {
                $status = 'not_started';
            } elseif ($verifiedCampus === $totalCampus) {
                $status = 'completed';
            } else {
                $status = 'in_progress';
            }

            $campusStats[] = [
                'id' => $campus->id,
                'name' => $campus->name,
                'percent' => $percent,
                'status' => $status,
            ];
        }

        return view('audits.dashboard', compact(
            'audit',
            'records',
            'total',
            'verified',
            'missing',
            'discrepancies',
            'campuses',
            'campusStats',
            'campusId',
            'campusProgress',
            'statusFilter'
        ));
    }
}