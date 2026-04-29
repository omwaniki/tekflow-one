<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Campus;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Agent::with([
            'region',
            'campus',
            'user.region',
            'user.campus'
        ])->visibleTo($user);

        // 🔍 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $agents = $query->latest()->paginate(10);

        // 🔥 AJAX (LIVE SEARCH)
        if ($request->ajax()) {
            return response()->json([
                'table' => view('agents.partials.table', compact('agents'))->render(),
                'pagination' => $agents->links()->toHtml()
            ]);
        }

        // 🧾 NORMAL LOAD
        return view('agents.index', compact('agents'));
    }

    public function create()
    {
        $user = auth()->user();

        $campuses = Campus::visibleTo($user)->orderBy('name')->get();
        $roles = Role::all();

        if ($user->hasRole(['regional', 'manager'])) {
            $regions = Region::where('id', $user->region_id)->get();
        } else {
            $regions = Region::orderBy('name')->get();
        }

        return view('agents.create', compact('campuses', 'roles', 'regions'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'campus_id' => 'nullable|exists:campuses,id',
            'region_id' => 'nullable|exists:regions,id',
            'role' => 'required|string',
            'password' => 'required|confirmed|min:6',
        ]);

        switch ($validated['role']) {

            case 'regional':
            case 'manager':

                if (!$validated['region_id']) {
                    return back()->withErrors([
                        'region_id' => 'Region is required for this role.'
                    ])->withInput();
                }

                if ($authUser->hasRole(['regional', 'manager']) &&
                    $validated['region_id'] != $authUser->region_id) {
                    abort(403, 'You cannot assign a region outside your scope.');
                }

                $validated['campus_id'] = null;

                break;

            case 'campus':
            case 'agent':

                if (!$validated['campus_id']) {
                    return back()->withErrors([
                        'campus_id' => 'Campus is required for this role.'
                    ])->withInput();
                }

                $campus = Campus::find($validated['campus_id']);

                if (!$campus) {
                    return back()->withErrors([
                        'campus_id' => 'Selected campus was not found.'
                    ])->withInput();
                }

                if ($authUser->hasRole(['regional', 'manager']) &&
                    $campus->region_id != $authUser->region_id) {
                    abort(403, 'You cannot assign a campus outside your region.');
                }

                $validated['region_id'] = $campus->region_id;

                break;

            case 'admin':
            case 'global':

                $validated['region_id'] = null;
                $validated['campus_id'] = null;

                break;

            default:
                abort(400, 'Invalid role.');
        }

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'campus_id' => $validated['campus_id'] ?? null,
            'region_id' => $validated['region_id'],
        ]);

        $newUser->assignRole($validated['role']);

        Agent::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'campus_id' => $validated['campus_id'] ?? null,
            'region_id' => $validated['region_id'],
            'user_id' => $newUser->id,
        ]);

        return redirect()
            ->route('agents.index')
            ->with('success', 'Agent created successfully.');
    }

    public function edit($id)
    {
        $user = auth()->user();

        $agent = Agent::with(['user.region', 'user.campus'])
            ->visibleTo($user)
            ->findOrFail($id);

        $campuses = Campus::visibleTo($user)->orderBy('name')->get();
        $roles = Role::all();

        if ($user->hasRole(['regional', 'manager'])) {
            $regions = Region::where('id', $user->region_id)->get();
        } else {
            $regions = Region::orderBy('name')->get();
        }

        return view('agents.edit', compact('agent', 'campuses', 'roles', 'regions'));
    }

    public function update(Request $request, $id)
    {
        $authUser = auth()->user();

        $agent = Agent::with('user')
            ->visibleTo($authUser)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $agent->user_id,
            'phone' => 'nullable|string|max:50',
            'campus_id' => 'nullable|exists:campuses,id',
            'region_id' => 'nullable|exists:regions,id',
            'role' => 'required|string',
            'password' => 'nullable|confirmed|min:6',
        ]);

        switch ($validated['role']) {

            case 'regional':
            case 'manager':

                if (!$validated['region_id']) {
                    return back()->withErrors([
                        'region_id' => 'Region is required for this role.'
                    ])->withInput();
                }

                if ($authUser->hasRole(['regional', 'manager']) &&
                    $validated['region_id'] != $authUser->region_id) {
                    abort(403, 'You cannot assign a region outside your scope.');
                }

                $validated['campus_id'] = null;

                break;

            case 'campus':
            case 'agent':

                if (!$validated['campus_id']) {
                    return back()->withErrors([
                        'campus_id' => 'Campus is required for this role.'
                    ])->withInput();
                }

                $campus = Campus::find($validated['campus_id']);

                if (!$campus) {
                    return back()->withErrors([
                        'campus_id' => 'Selected campus was not found.'
                    ])->withInput();
                }

                if ($authUser->hasRole(['regional', 'manager']) &&
                    $campus->region_id != $authUser->region_id) {
                    abort(403, 'You cannot assign a campus outside your region.');
                }

                $validated['region_id'] = $campus->region_id;

                break;

            case 'admin':
            case 'global':

                $validated['region_id'] = null;
                $validated['campus_id'] = null;

                break;

            default:
                abort(400, 'Invalid role.');
        }

        $agent->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'campus_id' => $validated['campus_id'] ?? null,
            'region_id' => $validated['region_id'],
        ]);

        $agentUser = $agent->user;

        if ($agentUser) {
            $agentUser->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'campus_id' => $validated['campus_id'] ?? null,
                'region_id' => $validated['region_id'],
            ]);

            if (!empty($validated['password'])) {
                $agentUser->update([
                    'password' => Hash::make($validated['password']),
                ]);
            }

            $agentUser->syncRoles([$validated['role']]);
        }

        return redirect()
            ->route('agents.index')
            ->with('success', 'Agent updated successfully.');
    }

    public function destroy($id)
    {
        $user = auth()->user();

        $agent = Agent::visibleTo($user)->findOrFail($id);

        if ($agent->user) {
            $agent->user->delete();
        }

        $agent->delete();

        return redirect()
            ->route('agents.index')
            ->with('success', 'Agent deleted.');
    }
    
}