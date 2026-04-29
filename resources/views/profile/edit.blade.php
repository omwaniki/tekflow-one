@forelse($agents as $agent)

@php
    $role = $agent->user?->getRoleNames()->first();
@endphp

<tr class="hover:bg-gray-50">

    <!-- CHECKBOX -->
    <td class="px-6 py-4">
        <input type="checkbox" class="row-checkbox" value="{{ $agent->id }}">
    </td>

    <!-- NAME -->
    <td class="px-6 py-4 font-medium text-gray-800">
        {{ $agent->name }}
    </td>

    <!-- EMAIL -->
    <td class="px-6 py-4 text-gray-600">
        {{ $agent->email ?? '-' }}
    </td>

    <!-- PHONE -->
    <td class="px-6 py-4 text-gray-600">
        {{ $agent->phone ?? '-' }}
    </td>

    <!-- 🔥 CAMPUS (RESTORED LOGIC) -->
    <td class="px-6 py-4 text-gray-600">
        @if($role === 'global' || $role === 'admin')
            <span class="text-gray-500 italic">
                All Campuses
            </span>

        @elseif(in_array($role, ['regional', 'manager']))
            <span class="text-gray-500 italic">
                All {{ $agent->region->name ?? '' }} Campuses
            </span>

        @else
            {{ $agent->campus->name ?? '-' }}
        @endif
    </td>

    <!-- 🔥 REGION (RESTORED LOGIC) -->
    <td class="px-6 py-4 text-gray-600">
        @if($role === 'global' || $role === 'admin')
            <span class="text-gray-500 italic">
                All Regions
            </span>
        @else
            {{ $agent->region->name ?? '-' }}
        @endif
    </td>

    <!-- ROLE -->
    <td class="px-6 py-4">
        @if($role)
            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                {{ ucfirst($role) }}
            </span>
        @else
            -
        @endif
    </td>

    <!-- ACTIONS -->
    <td class="px-6 py-4 text-right">
        <div class="flex justify-end gap-3">

            <a href="{{ route('agents.edit', $agent) }}"
               class="text-blue-600 hover:underline text-sm">
                Edit
            </a>

            <form method="POST"
                  action="{{ route('agents.destroy', $agent) }}"
                  onsubmit="return confirm('Are you sure you want to delete this agent?')">
                @csrf
                @method('DELETE')

                <button class="text-red-600 hover:underline text-sm">
                    Delete
                </button>
            </form>

        </div>
    </td>

</tr>

@empty
<tr>
    <td colspan="8" class="text-center py-10 text-gray-400">
        No agents found
    </td>
</tr>
@endforelse